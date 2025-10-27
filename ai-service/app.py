"""
AI Recommendation Service for The Stag SmartDine
Flask-based microservice providing collaborative filtering recommendations
"""

from flask import Flask, request, jsonify
from flask_cors import CORS
import mysql.connector
from mysql.connector import Error
import pandas as pd
import numpy as np
from sklearn.metrics.pairwise import cosine_similarity
from datetime import datetime, timedelta
import os
import json
import logging

app = Flask(__name__)
CORS(app)

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Database configuration (reads from environment variables)
DB_CONFIG = {
    'host': os.getenv('DB_HOST', 'localhost'),
    'database': os.getenv('DB_DATABASE', 'project_the_stag'),
    'user': os.getenv('DB_USERNAME', 'root'),
    'password': os.getenv('DB_PASSWORD', ''),
    'port': int(os.getenv('DB_PORT', 3306))
}

# Model storage
MODEL_DATA = {
    'user_item_matrix': None,
    'similarity_matrix': None,
    'item_popularity': None,
    'last_trained': None,
    'training_count': 0
}


def get_db_connection():
    """Create database connection"""
    try:
        connection = mysql.connector.connect(**DB_CONFIG)
        if connection.is_connected():
            return connection
    except Error as e:
        logger.error(f"Error connecting to MySQL: {e}")
        return None


def load_order_data():
    """Load order history from database"""
    connection = get_db_connection()
    if not connection:
        return None

    try:
        query = """
            SELECT
                o.user_id,
                oi.menu_item_id,
                oi.quantity,
                o.order_time,
                o.order_status
            FROM orders o
            INNER JOIN order_items oi ON o.id = oi.order_id
            WHERE o.user_id IS NOT NULL
                AND o.order_status IN ('completed', 'delivered')
                AND o.order_time >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            ORDER BY o.order_time DESC
        """

        df = pd.read_sql(query, connection)
        logger.info(f"Loaded {len(df)} order records")
        return df

    except Error as e:
        logger.error(f"Error loading order data: {e}")
        return None
    finally:
        if connection.is_connected():
            connection.close()


def load_menu_items():
    """Load menu items metadata"""
    connection = get_db_connection()
    if not connection:
        return None

    try:
        query = """
            SELECT
                id,
                name,
                category_id,
                price,
                availability
            FROM menu_items
            WHERE availability = 1
        """

        df = pd.read_sql(query, connection)
        logger.info(f"Loaded {len(df)} menu items")
        return df

    except Error as e:
        logger.error(f"Error loading menu items: {e}")
        return None
    finally:
        if connection.is_connected():
            connection.close()


def train_model():
    """Train collaborative filtering model"""
    logger.info("Starting model training...")

    # Load data
    orders_df = load_order_data()
    menu_df = load_menu_items()

    if orders_df is None or orders_df.empty:
        logger.warning("No order data available for training")
        return False

    if menu_df is None or menu_df.empty:
        logger.warning("No menu items available")
        return False

    try:
        # Create user-item interaction matrix
        # Sum quantities for same user-item pairs
        user_item_df = orders_df.groupby(['user_id', 'menu_item_id'])['quantity'].sum().reset_index()

        # Pivot to create matrix
        user_item_matrix = user_item_df.pivot(
            index='user_id',
            columns='menu_item_id',
            values='quantity'
        ).fillna(0)

        logger.info(f"User-Item Matrix shape: {user_item_matrix.shape}")

        # Calculate item-item similarity using cosine similarity
        # Transpose so items are rows
        item_matrix = user_item_matrix.T
        similarity_matrix = cosine_similarity(item_matrix)

        # Convert to DataFrame for easier lookup
        similarity_df = pd.DataFrame(
            similarity_matrix,
            index=item_matrix.index,
            columns=item_matrix.index
        )

        # Calculate item popularity (total orders)
        item_popularity = user_item_df.groupby('menu_item_id')['quantity'].sum().sort_values(ascending=False)

        # Store in global MODEL_DATA
        MODEL_DATA['user_item_matrix'] = user_item_matrix
        MODEL_DATA['similarity_matrix'] = similarity_df
        MODEL_DATA['item_popularity'] = item_popularity
        MODEL_DATA['last_trained'] = datetime.now()
        MODEL_DATA['training_count'] += 1

        logger.info(f"Model trained successfully. Training count: {MODEL_DATA['training_count']}")
        return True

    except Exception as e:
        logger.error(f"Error during model training: {e}")
        return False


def get_recommendations_for_user(user_id, limit=10, exclude_items=None):
    """
    Get personalized recommendations for a user using collaborative filtering

    Args:
        user_id: User ID to get recommendations for
        limit: Number of recommendations to return
        exclude_items: List of menu item IDs to exclude

    Returns:
        List of recommended menu item IDs with scores
    """
    if MODEL_DATA['user_item_matrix'] is None:
        logger.warning("Model not trained, training now...")
        if not train_model():
            return []

    user_item_matrix = MODEL_DATA['user_item_matrix']
    similarity_matrix = MODEL_DATA['similarity_matrix']
    item_popularity = MODEL_DATA['item_popularity']

    exclude_items = exclude_items or []

    # Check if user exists in training data
    if user_id not in user_item_matrix.index:
        logger.info(f"New user {user_id}, returning popular items")
        # Return popular items for new users
        popular_items = item_popularity.head(limit * 2).index.tolist()
        popular_items = [int(item) for item in popular_items if item not in exclude_items]
        return [{'menu_item_id': item, 'score': 0.5} for item in popular_items[:limit]]

    # Get user's order history
    user_items = user_item_matrix.loc[user_id]
    ordered_items = user_items[user_items > 0].index.tolist()

    if not ordered_items:
        logger.info(f"User {user_id} has no orders, returning popular items")
        popular_items = item_popularity.head(limit * 2).index.tolist()
        popular_items = [int(item) for item in popular_items if item not in exclude_items]
        return [{'menu_item_id': item, 'score': 0.5} for item in popular_items[:limit]]

    # Calculate recommendation scores
    recommendation_scores = {}

    for item in ordered_items:
        if item not in similarity_matrix.index:
            continue

        # Get similar items
        similar_items = similarity_matrix[item].sort_values(ascending=False)

        # Weight by user's interaction strength
        user_weight = user_items[item]

        for similar_item, similarity_score in similar_items.items():
            if similar_item == item:  # Skip self
                continue
            if similar_item in ordered_items:  # Skip already ordered
                continue
            if similar_item in exclude_items:  # Skip excluded
                continue

            # Accumulate weighted score
            score = similarity_score * user_weight
            recommendation_scores[similar_item] = recommendation_scores.get(similar_item, 0) + score

    # If we have recommendations, return them
    if recommendation_scores:
        sorted_recommendations = sorted(
            recommendation_scores.items(),
            key=lambda x: x[1],
            reverse=True
        )

        # Normalize scores to 0-1 range
        max_score = sorted_recommendations[0][1] if sorted_recommendations else 1
        normalized_recs = [
            {
                'menu_item_id': int(item_id),
                'score': round(score / max_score, 3)
            }
            for item_id, score in sorted_recommendations[:limit]
        ]

        logger.info(f"Generated {len(normalized_recs)} recommendations for user {user_id}")
        return normalized_recs

    # Fallback to popular items
    logger.info(f"No similar items found for user {user_id}, returning popular items")
    popular_items = item_popularity.head(limit * 2).index.tolist()
    popular_items = [int(item) for item in popular_items
                    if item not in ordered_items and item not in exclude_items]
    return [{'menu_item_id': item, 'score': 0.3} for item in popular_items[:limit]]


@app.route('/health', methods=['GET'])
def health_check():
    """Health check endpoint"""
    return jsonify({
        'status': 'healthy',
        'service': 'AI Recommendation Service',
        'version': '1.0.0',
        'timestamp': datetime.now().isoformat()
    })


@app.route('/model/status', methods=['GET'])
def model_status():
    """Get model training status"""
    is_trained = MODEL_DATA['user_item_matrix'] is not None

    status = {
        'trained': is_trained,
        'last_trained': MODEL_DATA['last_trained'].isoformat() if MODEL_DATA['last_trained'] else None,
        'training_count': MODEL_DATA['training_count'],
    }

    if is_trained:
        status['model_info'] = {
            'users_count': len(MODEL_DATA['user_item_matrix'].index),
            'items_count': len(MODEL_DATA['user_item_matrix'].columns),
            'total_interactions': int(MODEL_DATA['user_item_matrix'].sum().sum())
        }

    return jsonify(status)


@app.route('/recommend', methods=['POST'])
def recommend():
    """
    Get recommendations for a user

    Expected JSON body:
    {
        "user_id": 123,
        "limit": 10,
        "exclude_items": [1, 2, 3]  // optional
    }
    """
    try:
        data = request.get_json()

        if not data or 'user_id' not in data:
            return jsonify({'error': 'user_id is required'}), 400

        user_id = int(data['user_id'])
        limit = int(data.get('limit', 10))
        exclude_items = data.get('exclude_items', [])

        # Get recommendations
        recommendations = get_recommendations_for_user(
            user_id=user_id,
            limit=limit,
            exclude_items=exclude_items
        )

        return jsonify({
            'success': True,
            'user_id': user_id,
            'recommendations': recommendations,
            'count': len(recommendations),
            'generated_at': datetime.now().isoformat()
        })

    except ValueError as e:
        logger.error(f"Validation error: {e}")
        return jsonify({'error': f'Invalid input: {str(e)}'}), 400
    except Exception as e:
        logger.error(f"Error generating recommendations: {e}")
        return jsonify({'error': 'Internal server error'}), 500


@app.route('/retrain', methods=['POST'])
def retrain():
    """Retrain the recommendation model"""
    try:
        logger.info("Manual retrain requested")
        success = train_model()

        if success:
            return jsonify({
                'success': True,
                'message': 'Model retrained successfully',
                'trained_at': MODEL_DATA['last_trained'].isoformat(),
                'training_count': MODEL_DATA['training_count']
            })
        else:
            return jsonify({
                'success': False,
                'message': 'Model training failed'
            }), 500

    except Exception as e:
        logger.error(f"Error during retrain: {e}")
        return jsonify({'error': 'Internal server error'}), 500


@app.route('/batch-recommend', methods=['POST'])
def batch_recommend():
    """
    Get recommendations for multiple users at once

    Expected JSON body:
    {
        "user_ids": [1, 2, 3],
        "limit": 10
    }
    """
    try:
        data = request.get_json()

        if not data or 'user_ids' not in data:
            return jsonify({'error': 'user_ids is required'}), 400

        user_ids = data['user_ids']
        limit = int(data.get('limit', 10))

        results = {}
        for user_id in user_ids:
            recommendations = get_recommendations_for_user(
                user_id=int(user_id),
                limit=limit
            )
            results[str(user_id)] = recommendations

        return jsonify({
            'success': True,
            'results': results,
            'generated_at': datetime.now().isoformat()
        })

    except Exception as e:
        logger.error(f"Error in batch recommendations: {e}")
        return jsonify({'error': 'Internal server error'}), 500


if __name__ == '__main__':
    # Train model on startup
    logger.info("Starting AI Recommendation Service...")
    logger.info("Database configuration:")
    logger.info(f"  Host: {DB_CONFIG['host']}")
    logger.info(f"  Database: {DB_CONFIG['database']}")
    logger.info(f"  User: {DB_CONFIG['user']}")

    # Initial training
    train_model()

    # Start Flask server
    port = int(os.getenv('PORT', 8000))
    logger.info(f"Starting server on port {port}...")
    app.run(host='0.0.0.0', port=port, debug=False)
