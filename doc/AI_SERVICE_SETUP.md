# Python AI Service - Shared Database Integration

## 📁 Project Structure untuk Python AI Service

```
python_ai_service/
├── requirements.txt
├── database.py
├── main.py
├── models/
│   └── recommendation_model.py
├── .env
└── README.md
```

## 1. 📦 requirements.txt

```txt
fastapi==0.104.1
uvicorn==0.24.0
PyMySQL==1.1.0
SQLAlchemy==2.0.23
pandas==2.1.4
python-dotenv==1.0.0
scikit-learn==1.3.2
numpy==1.24.3
logging
typing
```

## 2. 🔗 database.py

```python
"""
Database connection module for AI recommendation service
Connects directly to Laravel MySQL database for real-time data access
"""

import pymysql
import pandas as pd
from sqlalchemy import create_engine, text
import logging
from datetime import datetime, timedelta
from typing import Dict, List, Optional
import os
from dotenv import load_dotenv

# Load environment variables
load_dotenv()

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

class DatabaseConnection:
    """
    Database connection handler for shared Laravel database access
    Provides real-time data access for AI recommendation model
    """
    
    def __init__(self):
        # Laravel database configuration
        self.host = os.getenv('DB_HOST', '127.0.0.1')
        self.port = int(os.getenv('DB_PORT', 3306))
        self.database = os.getenv('DB_DATABASE', 'the_stag')
        self.user = os.getenv('DB_USERNAME', 'root')
        self.password = os.getenv('DB_PASSWORD', '')
        
        # Create SQLAlchemy engine for pandas operations
        connection_string = f'mysql+pymysql://{self.user}:{self.password}@{self.host}:{self.port}/{self.database}'
        self.engine = create_engine(
            connection_string,
            pool_size=10,
            max_overflow=20,
            pool_pre_ping=True,
            pool_recycle=3600
        )
        
        logger.info(f"Database connection initialized for {self.database}")
    
    def test_connection(self) -> bool:
        """Test database connection"""
        try:
            with self.engine.connect() as conn:
                result = conn.execute(text("SELECT 1"))
                return True
        except Exception as e:
            logger.error(f"Database connection failed: {e}")
            return False
    
    def get_training_data(self, limit: Optional[int] = None) -> pd.DataFrame:
        """
        Get comprehensive order data for model training
        
        Args:
            limit: Optional limit for number of records
            
        Returns:
            DataFrame with order history for training
        """
        try:
            query = """
            SELECT 
                o.user_id,
                o.id as order_id,
                oi.menu_item_id,
                oi.quantity,
                oi.unit_price,
                oi.total_price,
                o.order_time,
                o.order_type,
                o.order_status,
                o.total_amount,
                mi.category_id,
                mi.name as item_name,
                mi.price as menu_price,
                mi.rating_average,
                mi.is_featured,
                mi.preparation_time,
                c.name as category_name,
                c.type as category_type,
                HOUR(o.order_time) as order_hour,
                DAYOFWEEK(o.order_time) as order_day_of_week,
                DATE(o.order_time) as order_date
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN menu_items mi ON oi.menu_item_id = mi.id
            LEFT JOIN categories c ON mi.category_id = c.id
            WHERE o.order_status IN ('completed', 'served')
            AND o.deleted_at IS NULL
            AND oi.deleted_at IS NULL
            AND mi.deleted_at IS NULL
            ORDER BY o.order_time DESC
            """
            
            if limit:
                query += f" LIMIT {limit}"
            
            df = pd.read_sql(query, self.engine)
            logger.info(f"Retrieved {len(df)} training records")
            return df
            
        except Exception as e:
            logger.error(f"Error getting training data: {e}")
            return pd.DataFrame()
    
    def get_user_context(self, user_id: int) -> Dict:
        """
        Get real-time user context for recommendations
        
        Args:
            user_id: User ID to get context for
            
        Returns:
            Dictionary with user context data
        """
        try:
            context = {}
            
            # Current cart items
            cart_query = """
            SELECT 
                uc.menu_item_id,
                uc.quantity,
                uc.unit_price,
                uc.special_notes,
                mi.name as item_name,
                mi.category_id,
                c.name as category_name
            FROM user_carts uc
            JOIN menu_items mi ON uc.menu_item_id = mi.id
            LEFT JOIN categories c ON mi.category_id = c.id
            WHERE uc.user_id = %s AND uc.deleted_at IS NULL
            """
            
            # Recent orders (last 30 days)
            orders_query = """
            SELECT 
                oi.menu_item_id,
                mi.name as item_name,
                mi.category_id,
                c.name as category_name,
                COUNT(*) as frequency,
                AVG(oi.quantity) as avg_quantity,
                MAX(o.order_time) as last_ordered,
                SUM(oi.total_price) as total_spent
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN menu_items mi ON oi.menu_item_id = mi.id
            LEFT JOIN categories c ON mi.category_id = c.id
            WHERE o.user_id = %s 
            AND o.created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)
            AND o.order_status IN ('completed', 'served')
            AND o.deleted_at IS NULL
            AND oi.deleted_at IS NULL
            GROUP BY oi.menu_item_id, mi.name, mi.category_id, c.name
            ORDER BY frequency DESC, last_ordered DESC
            LIMIT 20
            """
            
            # User preferences (most ordered categories)
            preferences_query = """
            SELECT 
                c.id as category_id,
                c.name as category_name,
                COUNT(*) as order_count,
                AVG(oi.total_price) as avg_spending
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN menu_items mi ON oi.menu_item_id = mi.id
            JOIN categories c ON mi.category_id = c.id
            WHERE o.user_id = %s
            AND o.order_status IN ('completed', 'served')
            AND o.created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)
            GROUP BY c.id, c.name
            ORDER BY order_count DESC
            LIMIT 5
            """
            
            # Execute queries
            cart_df = pd.read_sql(cart_query, self.engine, params=[user_id])
            orders_df = pd.read_sql(orders_query, self.engine, params=[user_id])
            preferences_df = pd.read_sql(preferences_query, self.engine, params=[user_id])
            
            context = {
                'user_id': user_id,
                'current_cart': cart_df.to_dict('records'),
                'recent_orders': orders_df.to_dict('records'),
                'category_preferences': preferences_df.to_dict('records'),
                'context_timestamp': datetime.now().isoformat()
            }
            
            logger.info(f"Retrieved context for user {user_id}: {len(cart_df)} cart items, {len(orders_df)} recent orders")
            return context
            
        except Exception as e:
            logger.error(f"Error getting user context for user {user_id}: {e}")
            return {
                'user_id': user_id,
                'current_cart': [],
                'recent_orders': [],
                'category_preferences': [],
                'context_timestamp': datetime.now().isoformat()
            }
    
    def get_available_menu(self) -> pd.DataFrame:
        """
        Get current available menu items
        
        Returns:
            DataFrame with available menu items
        """
        try:
            query = """
            SELECT 
                mi.id,
                mi.name,
                mi.category_id,
                mi.price,
                mi.rating_average,
                mi.rating_count,
                mi.is_featured,
                mi.preparation_time,
                mi.availability,
                c.name as category_name,
                c.type as category_type,
                c.sort_order
            FROM menu_items mi
            LEFT JOIN categories c ON mi.category_id = c.id
            WHERE mi.availability = 1 
            AND mi.deleted_at IS NULL
            AND (c.deleted_at IS NULL OR c.id IS NULL)
            ORDER BY c.sort_order ASC, mi.is_featured DESC, mi.rating_average DESC
            """
            
            df = pd.read_sql(query, self.engine)
            logger.info(f"Retrieved {len(df)} available menu items")
            return df
            
        except Exception as e:
            logger.error(f"Error getting available menu: {e}")
            return pd.DataFrame()
    
    def get_popular_items(self, days: int = 7, limit: int = 10) -> pd.DataFrame:
        """
        Get popular items based on recent orders
        
        Args:
            days: Number of days to look back
            limit: Maximum number of items to return
            
        Returns:
            DataFrame with popular items
        """
        try:
            query = """
            SELECT 
                mi.id,
                mi.name,
                mi.category_id,
                c.name as category_name,
                COUNT(*) as order_count,
                SUM(oi.quantity) as total_quantity,
                AVG(oi.quantity) as avg_quantity_per_order,
                SUM(oi.total_price) as total_revenue,
                mi.rating_average,
                mi.price
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN menu_items mi ON oi.menu_item_id = mi.id
            LEFT JOIN categories c ON mi.category_id = c.id
            WHERE o.order_status IN ('completed', 'served')
            AND o.created_at >= DATE_SUB(NOW(), INTERVAL %s DAY)
            AND o.deleted_at IS NULL
            AND oi.deleted_at IS NULL
            AND mi.deleted_at IS NULL
            GROUP BY mi.id, mi.name, mi.category_id, c.name, mi.rating_average, mi.price
            ORDER BY order_count DESC, total_quantity DESC
            LIMIT %s
            """
            
            df = pd.read_sql(query, self.engine, params=[days, limit])
            logger.info(f"Retrieved {len(df)} popular items from last {days} days")
            return df
            
        except Exception as e:
            logger.error(f"Error getting popular items: {e}")
            return pd.DataFrame()
```

## 3. 🚀 main.py (FastAPI Service)

```python
"""
AI Recommendation Service with Shared Database Integration
FastAPI service that connects directly to Laravel MySQL database
"""

from fastapi import FastAPI, HTTPException, BackgroundTasks
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
from typing import Optional, List, Dict, Any
import logging
from datetime import datetime
import pandas as pd
import numpy as np
from database import DatabaseConnection
from models.recommendation_model import RecommendationModel

# Configure logging
logging.basicConfig(level=logging.INFO)
logger = logging.getLogger(__name__)

# Initialize FastAPI app
app = FastAPI(
    title="AI Recommendation Service",
    description="Real-time recommendation service with shared database access",
    version="2.0.0"
)

# Add CORS middleware
app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  # Configure for production
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Global variables
db = DatabaseConnection()
model = None
last_training_time = None

# Pydantic models for request/response
class RecommendationRequest(BaseModel):
    user_id: int
    topn: Optional[int] = 5
    alpha: Optional[float] = None
    context: Optional[Dict[str, Any]] = None

class RecommendationResponse(BaseModel):
    user_id: int
    recommendations: List[Dict[str, Any]]
    context_used: bool
    model_version: str
    timestamp: str

@app.on_event("startup")
async def startup_event():
    """Initialize model on startup with fresh data from database"""
    global model, last_training_time
    
    try:
        logger.info("Starting AI Recommendation Service...")
        
        # Test database connection
        if not db.test_connection():
            logger.error("Failed to connect to database")
            raise Exception("Database connection failed")
        
        logger.info("Database connection successful")
        
        # Load training data
        logger.info("Loading training data from Laravel database...")
        training_data = db.get_training_data(limit=10000)  # Limit for initial load
        
        if len(training_data) > 0:
            # Initialize recommendation model
            model = RecommendationModel()
            model.train(training_data)
            last_training_time = datetime.now()
            
            logger.info(f"Model trained successfully with {len(training_data)} records")
            logger.info(f"Training completed at: {last_training_time}")
        else:
            logger.warning("No training data found, starting with empty model")
            model = RecommendationModel()
            last_training_time = datetime.now()
            
    except Exception as e:
        logger.error(f"Startup error: {e}")
        model = RecommendationModel()  # Fallback empty model
        last_training_time = datetime.now()

@app.get("/")
async def health_check():
    """Health check endpoint"""
    return {
        "status": "healthy",
        "service": "AI Recommendation Service",
        "version": "2.0.0",
        "database_connected": db.test_connection(),
        "model_trained": model is not None and model.is_trained(),
        "last_training": last_training_time.isoformat() if last_training_time else None,
        "timestamp": datetime.now().isoformat()
    }

@app.post("/recommend", response_model=RecommendationResponse)
async def get_recommendations(request: RecommendationRequest):
    """
    Get recommendations with real-time context from database
    """
    try:
        logger.info(f"Recommendation request for user {request.user_id}")
        
        if model is None:
            raise HTTPException(status_code=503, detail="Model not initialized")
        
        # Get real-time user context from database
        user_context = db.get_user_context(request.user_id)
        available_menu = db.get_available_menu()
        
        # Merge request context with database context
        if request.context:
            user_context.update(request.context)
        
        # Generate recommendations
        recommendations = model.predict(
            user_id=request.user_id,
            user_context=user_context,
            available_items=available_menu,
            topn=request.topn,
            alpha=request.alpha
        )
        
        # Log recommendation request
        db.log_recommendation_request(request.user_id, recommendations, user_context)
        
        response = RecommendationResponse(
            user_id=request.user_id,
            recommendations=recommendations,
            context_used=True,
            model_version=model.get_version(),
            timestamp=datetime.now().isoformat()
        )
        
        logger.info(f"Generated {len(recommendations)} recommendations for user {request.user_id}")
        return response
        
    except Exception as e:
        logger.error(f"Error generating recommendations for user {request.user_id}: {e}")
        
        # Fallback to popular items
        try:
            popular_items = db.get_popular_items(limit=request.topn)
            fallback_recommendations = popular_items.to_dict('records') if len(popular_items) > 0 else []
            
            return RecommendationResponse(
                user_id=request.user_id,
                recommendations=fallback_recommendations,
                context_used=False,
                model_version="fallback",
                timestamp=datetime.now().isoformat()
            )
        except:
            raise HTTPException(status_code=500, detail=f"Recommendation failed: {str(e)}")

@app.post("/retrain")
async def retrain_model(background_tasks: BackgroundTasks):
    """
    Retrain model with latest data from database
    """
    try:
        logger.info("Model retrain requested")
        
        # Add background task for retraining
        background_tasks.add_task(retrain_model_background)
        
        return {
            "status": "retrain_started",
            "message": "Model retraining started in background",
            "timestamp": datetime.now().isoformat()
        }
        
    except Exception as e:
        logger.error(f"Error starting model retrain: {e}")
        raise HTTPException(status_code=500, detail=str(e))

async def retrain_model_background():
    """Background task for model retraining"""
    global model, last_training_time
    
    try:
        logger.info("Starting background model retraining...")
        
        # Get fresh training data
        training_data = db.get_training_data()
        
        if len(training_data) > 0:
            # Retrain model
            if model is None:
                model = RecommendationModel()
            
            model.train(training_data)
            last_training_time = datetime.now()
            
            logger.info(f"Model retrained successfully with {len(training_data)} records")
        else:
            logger.warning("No training data available for retraining")
            
    except Exception as e:
        logger.error(f"Background retrain error: {e}")

@app.get("/model/status")
async def get_model_status():
    """Get model status and statistics"""
    try:
        training_data = db.get_training_data(limit=1)
        available_menu = db.get_available_menu()
        
        return {
            "model_trained": model is not None and model.is_trained(),
            "last_training": last_training_time.isoformat() if last_training_time else None,
            "training_records_available": len(training_data) if len(training_data) > 0 else 0,
            "available_menu_items": len(available_menu),
            "model_version": model.get_version() if model else None,
            "database_connected": db.test_connection(),
            "timestamp": datetime.now().isoformat()
        }
        
    except Exception as e:
        logger.error(f"Error getting model status: {e}")
        raise HTTPException(status_code=500, detail=str(e))

# Legacy GET endpoint for backward compatibility
@app.get("/recommend/{user_id}")
async def get_recommendations_legacy(user_id: int, topn: Optional[int] = 5, alpha: Optional[float] = None):
    """Legacy GET endpoint for backward compatibility"""
    request = RecommendationRequest(user_id=user_id, topn=topn, alpha=alpha)
    return await get_recommendations(request)

if __name__ == "__main__":
    import uvicorn
    uvicorn.run(app, host="0.0.0.0", port=8000, log_level="info")
```

## 4. 🤖 models/recommendation_model.py

```python
"""
Recommendation Model Implementation
Simple collaborative filtering with content-based features
"""

import pandas as pd
import numpy as np
from sklearn.metrics.pairwise import cosine_similarity
from sklearn.feature_extraction.text import TfidfVectorizer
from typing import Dict, List, Any
import logging
from datetime import datetime

logger = logging.getLogger(__name__)

class RecommendationModel:
    """
    Hybrid recommendation model combining collaborative and content-based filtering
    """
    
    def __init__(self):
        self.user_item_matrix = None
        self.item_features = None
        self.user_similarities = None
        self.item_similarities = None
        self.trained = False
        self.version = "1.0.0"
        self.training_time = None
        
    def train(self, training_data: pd.DataFrame):
        """
        Train the recommendation model
        
        Args:
            training_data: DataFrame with order history
        """
        try:
            logger.info(f"Training model with {len(training_data)} records")
            
            if len(training_data) == 0:
                logger.warning("No training data provided")
                return
            
            # Create user-item interaction matrix
            self.user_item_matrix = training_data.pivot_table(
                index='user_id',
                columns='menu_item_id',
                values='quantity',
                aggfunc='sum',
                fill_value=0
            )
            
            # Create item features matrix
            item_features = training_data.groupby('menu_item_id').agg({
                'item_name': 'first',
                'category_name': 'first',
                'menu_price': 'first',
                'rating_average': 'first',
                'is_featured': 'first',
                'order_count': 'size',
                'avg_quantity': 'quantity'
            }).reset_index()
            
            self.item_features = item_features
            
            # Calculate user similarities (collaborative filtering)
            if len(self.user_item_matrix) > 1:
                self.user_similarities = cosine_similarity(self.user_item_matrix)
            
            # Calculate item similarities (content-based)
            if len(item_features) > 1:
                # Create content features
                content_features = []
                for _, item in item_features.iterrows():
                    features = f"{item.get('category_name', '')} {item.get('item_name', '')}"
                    content_features.append(features)
                
                if content_features:
                    tfidf = TfidfVectorizer(stop_words='english')
                    content_matrix = tfidf.fit_transform(content_features)
                    self.item_similarities = cosine_similarity(content_matrix)
            
            self.trained = True
            self.training_time = datetime.now()
            
            logger.info(f"Model training completed: {len(self.user_item_matrix)} users, {len(self.item_features)} items")
            
        except Exception as e:
            logger.error(f"Model training error: {e}")
            self.trained = False
    
    def predict(self, user_id: int, user_context: Dict, available_items: pd.DataFrame, 
                topn: int = 5, alpha: float = 0.7) -> List[Dict[str, Any]]:
        """
        Generate recommendations for a user
        
        Args:
            user_id: User ID
            user_context: User context from database
            available_items: Available menu items
            topn: Number of recommendations
            alpha: Weight for collaborative vs content-based (0.7 = 70% collaborative)
            
        Returns:
            List of recommended items
        """
        try:
            if not self.trained or len(available_items) == 0:
                return self._get_fallback_recommendations(available_items, topn)
            
            # Get collaborative filtering scores
            collaborative_scores = self._get_collaborative_scores(user_id, available_items)
            
            # Get content-based scores
            content_scores = self._get_content_scores(user_context, available_items)
            
            # Combine scores
            if alpha is None:
                alpha = 0.7
            
            final_scores = {}
            all_items = set(collaborative_scores.keys()) | set(content_scores.keys())
            
            for item_id in all_items:
                collab_score = collaborative_scores.get(item_id, 0)
                content_score = content_scores.get(item_id, 0)
                final_scores[item_id] = alpha * collab_score + (1 - alpha) * content_score
            
            # Filter available items and sort by score
            available_item_ids = set(available_items['id'].tolist())
            filtered_scores = {k: v for k, v in final_scores.items() if k in available_item_ids}
            
            # Sort and get top N
            sorted_items = sorted(filtered_scores.items(), key=lambda x: x[1], reverse=True)
            top_items = sorted_items[:topn]
            
            # Build recommendations with item details
            recommendations = []
            for item_id, score in top_items:
                item_info = available_items[available_items['id'] == item_id].iloc[0]
                
                recommendation = {
                    'menu_item_id': int(item_id),
                    'name': item_info['name'],
                    'category_id': int(item_info['category_id']) if pd.notna(item_info['category_id']) else None,
                    'category_name': item_info.get('category_name', ''),
                    'price': float(item_info['price']),
                    'rating_average': float(item_info['rating_average']) if pd.notna(item_info['rating_average']) else 0,
                    'is_featured': bool(item_info['is_featured']),
                    'recommendation_score': float(score),
                    'reason': self._get_recommendation_reason(user_context, item_info)
                }
                recommendations.append(recommendation)
            
            # Fill with popular items if not enough recommendations
            if len(recommendations) < topn:
                popular_items = self._get_popular_fallback(available_items, topn - len(recommendations))
                recommendations.extend(popular_items)
            
            return recommendations[:topn]
            
        except Exception as e:
            logger.error(f"Prediction error for user {user_id}: {e}")
            return self._get_fallback_recommendations(available_items, topn)
    
    def _get_collaborative_scores(self, user_id: int, available_items: pd.DataFrame) -> Dict[int, float]:
        """Get collaborative filtering scores"""
        scores = {}
        
        try:
            if self.user_item_matrix is None or user_id not in self.user_item_matrix.index:
                return scores
            
            user_idx = self.user_item_matrix.index.get_loc(user_id)
            user_similarities_scores = self.user_similarities[user_idx]
            
            # Find similar users
            similar_users = []
            for i, similarity in enumerate(user_similarities_scores):
                if i != user_idx and similarity > 0.1:  # Threshold for similarity
                    similar_users.append((i, similarity))
            
            # Sort by similarity
            similar_users.sort(key=lambda x: x[1], reverse=True)
            
            # Calculate scores based on similar users' preferences
            for item_id in available_items['id']:
                if item_id in self.user_item_matrix.columns:
                    score = 0
                    total_weight = 0
                    
                    for user_idx_sim, similarity in similar_users[:10]:  # Top 10 similar users
                        user_id_sim = self.user_item_matrix.index[user_idx_sim]
                        item_rating = self.user_item_matrix.loc[user_id_sim, item_id]
                        
                        if item_rating > 0:
                            score += similarity * item_rating
                            total_weight += similarity
                    
                    if total_weight > 0:
                        scores[item_id] = score / total_weight
            
        except Exception as e:
            logger.error(f"Collaborative filtering error: {e}")
        
        return scores
    
    def _get_content_scores(self, user_context: Dict, available_items: pd.DataFrame) -> Dict[int, float]:
        """Get content-based scores"""
        scores = {}
        
        try:
            # Score based on user's current cart
            cart_items = user_context.get('current_cart', [])
            recent_orders = user_context.get('recent_orders', [])
            preferences = user_context.get('category_preferences', [])
            
            # Create preference weights
            category_weights = {}
            for pref in preferences:
                category_weights[pref['category_id']] = pref['order_count']
            
            # Score items based on preferences
            for _, item in available_items.iterrows():
                score = 0
                
                # Category preference score
                if item['category_id'] in category_weights:
                    score += category_weights[item['category_id']] * 0.3
                
                # Featured item bonus
                if item['is_featured']:
                    score += 0.2
                
                # Rating bonus
                if pd.notna(item['rating_average']):
                    score += item['rating_average'] * 0.1
                
                # Recent order bonus
                for order in recent_orders:
                    if order['menu_item_id'] == item['id']:
                        score += order['frequency'] * 0.4
                
                scores[item['id']] = score
            
        except Exception as e:
            logger.error(f"Content-based scoring error: {e}")
        
        return scores
    
    def _get_recommendation_reason(self, user_context: Dict, item_info: pd.Series) -> str:
        """Generate recommendation reason"""
        reasons = []
        
        # Check if in preferred category
        preferences = user_context.get('category_preferences', [])
        for pref in preferences:
            if pref['category_id'] == item_info['category_id']:
                reasons.append(f"Popular in your favorite {pref['category_name']} category")
                break
        
        # Check if similar to recent orders
        recent_orders = user_context.get('recent_orders', [])
        for order in recent_orders:
            if order['category_id'] == item_info['category_id']:
                reasons.append(f"Similar to {order['item_name']} you ordered recently")
                break
        
        # Featured item
        if item_info['is_featured']:
            reasons.append("Featured item")
        
        # High rating
        if pd.notna(item_info['rating_average']) and item_info['rating_average'] >= 4.0:
            reasons.append(f"Highly rated ({item_info['rating_average']:.1f}/5)")
        
        return reasons[0] if reasons else "Recommended for you"
    
    def _get_fallback_recommendations(self, available_items: pd.DataFrame, topn: int) -> List[Dict[str, Any]]:
        """Get fallback recommendations (popular items)"""
        try:
            # Sort by featured, then rating, then random
            sorted_items = available_items.sort_values([
                'is_featured', 'rating_average'
            ], ascending=[False, False])
            
            recommendations = []
            for _, item in sorted_items.head(topn).iterrows():
                recommendation = {
                    'menu_item_id': int(item['id']),
                    'name': item['name'],
                    'category_id': int(item['category_id']) if pd.notna(item['category_id']) else None,
                    'category_name': item.get('category_name', ''),
                    'price': float(item['price']),
                    'rating_average': float(item['rating_average']) if pd.notna(item['rating_average']) else 0,
                    'is_featured': bool(item['is_featured']),
                    'recommendation_score': 0.5,
                    'reason': "Popular item"
                }
                recommendations.append(recommendation)
            
            return recommendations
            
        except Exception as e:
            logger.error(f"Fallback recommendations error: {e}")
            return []
    
    def _get_popular_fallback(self, available_items: pd.DataFrame, count: int) -> List[Dict[str, Any]]:
        """Get popular items as fallback"""
        try:
            popular_items = available_items.sort_values([
                'is_featured', 'rating_average'
            ], ascending=[False, False]).head(count)
            
            recommendations = []
            for _, item in popular_items.iterrows():
                recommendation = {
                    'menu_item_id': int(item['id']),
                    'name': item['name'],
                    'category_id': int(item['category_id']) if pd.notna(item['category_id']) else None,
                    'category_name': item.get('category_name', ''),
                    'price': float(item['price']),
                    'rating_average': float(item['rating_average']) if pd.notna(item['rating_average']) else 0,
                    'is_featured': bool(item['is_featured']),
                    'recommendation_score': 0.3,
                    'reason': "Popular choice"
                }
                recommendations.append(recommendation)
            
            return recommendations
            
        except Exception as e:
            logger.error(f"Popular fallback error: {e}")
            return []
    
    def is_trained(self) -> bool:
        """Check if model is trained"""
        return self.trained
    
    def get_version(self) -> str:
        """Get model version"""
        return self.version
```

## 5. 🔧 .env (Environment Configuration)

```env
# Database Configuration (same as Laravel)
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=the_stag
DB_USERNAME=root
DB_PASSWORD=

# Service Configuration
AI_SERVICE_HOST=0.0.0.0
AI_SERVICE_PORT=8000
LOG_LEVEL=INFO

# Model Configuration
MODEL_RETRAIN_INTERVAL=3600  # seconds
MAX_TRAINING_RECORDS=50000
```

## 6. 🚀 Installation & Run Commands

```bash
# Install dependencies
pip install -r requirements.txt

# Run the service
python main.py

# Or with uvicorn directly
uvicorn main:app --host 0.0.0.0 --port 8000 --reload
```

## 7. 🔄 API Endpoints

### Health Check
```
GET http://localhost:8000/
```

### Get Recommendations (New)
```
POST http://localhost:8000/recommend
{
    "user_id": 123,
    "topn": 5,
    "alpha": 0.7
}
```

### Get Recommendations (Legacy)
```
GET http://localhost:8000/recommend/123?topn=5&alpha=0.7
```

### Retrain Model
```
POST http://localhost:8000/retrain
```

### Model Status
```
GET http://localhost:8000/model/status
```

## 📋 Next Steps untuk Laravel Integration

Seterusnya, saya akan update Laravel `RecommendationService` untuk integrate dengan Python service yang baru ni. Nak proceed ke Laravel side?