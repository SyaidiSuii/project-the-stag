# AI Recommendation Service

Flask-based microservice providing collaborative filtering recommendations for The Stag SmartDine restaurant system.

## Features

- **Collaborative Filtering**: Item-item similarity using cosine similarity
- **Personalized Recommendations**: Based on user order history
- **Fallback Strategy**: Returns popular items for new users
- **Auto-training**: Trains model on startup and supports manual retraining
- **RESTful API**: Easy integration with Laravel backend

## How It Works

### Collaborative Filtering Algorithm

1. **User-Item Matrix**: Creates a matrix of users × menu items with order quantities
2. **Item Similarity**: Calculates cosine similarity between items based on who ordered them
3. **Recommendation Logic**:
   - For existing users: Recommends items similar to what they've ordered
   - For new users: Recommends popular items across all customers
4. **Scoring**: Weights recommendations by user's interaction strength

### Example:
- User A orders: Burger, Fries, Coke
- User B orders: Burger, Fries, Milkshake
- User C orders: Pizza, Fries, Coke

When User A returns, the system will recommend **Milkshake** (similar to Burger/Fries) and possibly **Pizza** (also ordered by Coke drinkers).

## Installation

### Prerequisites

- Python 3.8 or higher
- Access to The Stag SmartDine MySQL database

### Setup Steps

1. **Navigate to the ai-service directory**:
   ```bash
   cd ai-service
   ```

2. **Create virtual environment**:
   ```bash
   python -m venv venv
   ```

3. **Activate virtual environment**:
   - Windows:
     ```bash
     venv\Scripts\activate
     ```
   - Mac/Linux:
     ```bash
     source venv/bin/activate
     ```

4. **Install dependencies**:
   ```bash
   pip install -r requirements.txt
   ```

5. **Configure environment**:
   ```bash
   copy .env.example .env
   ```

   Edit `.env` with your database credentials (same as Laravel `.env`):
   ```
   DB_HOST=localhost
   DB_DATABASE=project_the_stag
   DB_USERNAME=root
   DB_PASSWORD=your_password
   DB_PORT=3306
   PORT=8000
   ```

6. **Run the service**:
   ```bash
   python app.py
   ```

   You should see:
   ```
   Starting AI Recommendation Service...
   Database configuration:
     Host: localhost
     Database: project_the_stag
     User: root
   Starting model training...
   Loaded X order records
   User-Item Matrix shape: (users, items)
   Model trained successfully. Training count: 1
   Starting server on port 8000...
   * Running on http://0.0.0.0:8000
   ```

## API Endpoints

### 1. Health Check
```bash
GET /health
```

Response:
```json
{
  "status": "healthy",
  "service": "AI Recommendation Service",
  "version": "1.0.0",
  "timestamp": "2025-10-25T10:30:00"
}
```

### 2. Model Status
```bash
GET /model/status
```

Response:
```json
{
  "trained": true,
  "last_trained": "2025-10-25T10:29:45",
  "training_count": 1,
  "model_info": {
    "users_count": 150,
    "items_count": 85,
    "total_interactions": 3420
  }
}
```

### 3. Get Recommendations
```bash
POST /recommend
Content-Type: application/json

{
  "user_id": 123,
  "limit": 10,
  "exclude_items": [1, 2, 3]  // optional
}
```

Response:
```json
{
  "success": true,
  "user_id": 123,
  "recommendations": [
    {"menu_item_id": 45, "score": 0.892},
    {"menu_item_id": 23, "score": 0.756},
    {"menu_item_id": 67, "score": 0.623}
  ],
  "count": 3,
  "generated_at": "2025-10-25T10:30:15"
}
```

### 4. Retrain Model
```bash
POST /retrain
```

Response:
```json
{
  "success": true,
  "message": "Model retrained successfully",
  "trained_at": "2025-10-25T10:35:00",
  "training_count": 2
}
```

### 5. Batch Recommendations
```bash
POST /batch-recommend
Content-Type: application/json

{
  "user_ids": [1, 2, 3],
  "limit": 5
}
```

Response:
```json
{
  "success": true,
  "results": {
    "1": [{"menu_item_id": 45, "score": 0.892}],
    "2": [{"menu_item_id": 23, "score": 0.756}],
    "3": [{"menu_item_id": 67, "score": 0.623}]
  },
  "generated_at": "2025-10-25T10:30:15"
}
```

## Laravel Integration

The service is already integrated with Laravel. Laravel's `RecommendationService` in `app/Services/RecommendationService.php` calls this Python service.

### Testing from Laravel

```bash
# Check AI service status
php artisan app:ai-service-status

# Retrain the model
php artisan app:ai-retrain
```

### Using in Controllers

```php
use App\Services\RecommendationService;

class MenuController extends Controller
{
    protected $recommendationService;

    public function __construct(RecommendationService $recommendationService)
    {
        $this->recommendationService = $recommendationService;
    }

    public function index(Request $request)
    {
        $userId = auth()->id();

        // Get AI recommendations
        $recommendations = $this->recommendationService->getRecommendations($userId, 10);

        // $recommendations will be array of menu_item_ids
        $recommendedItems = MenuItem::whereIn('id', $recommendations)->get();

        return view('customer.menu.index', compact('recommendedItems'));
    }
}
```

## Maintenance

### When to Retrain

The model should be retrained when:
- New order patterns emerge (seasonality, new menu items)
- Significant number of new orders (e.g., every 1000 orders)
- Recommendation quality degrades

You can set up automatic retraining:

**Option 1: Laravel Scheduled Task**
Add to `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    // Retrain AI model daily at 3 AM
    $schedule->command('app:ai-retrain')->dailyAt('03:00');
}
```

**Option 2: Cron Job**
```bash
0 3 * * * curl -X POST http://localhost:8000/retrain
```

### Monitoring

Check logs in the console where the Python service is running. All important events are logged:
- Database connections
- Model training progress
- Recommendation requests
- Errors and warnings

## Troubleshooting

### Service won't start

**Error**: `Error connecting to MySQL`
- **Solution**: Check database credentials in `.env`
- Ensure MySQL is running
- Test connection from Laravel: `php artisan migrate:status`

**Error**: `No module named 'flask'`
- **Solution**: Activate virtual environment and reinstall:
  ```bash
  venv\Scripts\activate
  pip install -r requirements.txt
  ```

### No recommendations returned

**Issue**: Empty recommendations array
- **Cause**: Not enough order data in database
- **Solution**: Need at least 10-20 completed orders for meaningful recommendations
- **Temporary**: Service will fall back to popular items

### Laravel can't connect to AI service

**Error**: Laravel shows "AI service unavailable"
- **Solution**: Ensure Python service is running on port 8000
- Check `AI_RECOMMENDER_BASE_URL=http://localhost:8000` in Laravel `.env`
- Test manually: `curl http://localhost:8000/health`

### Performance issues

**Issue**: Recommendations take too long
- **Cause**: Large dataset (10,000+ orders)
- **Solution**:
  - Limit order history to 6 months (already implemented)
  - Increase server resources
  - Consider caching recommendations

## Performance Notes

- **Startup time**: 2-5 seconds for initial training (depends on data size)
- **Recommendation latency**: 50-200ms per request
- **Memory usage**: ~200-500MB (depends on matrix size)
- **Training time**: 1-10 seconds (depends on order count)

## Data Requirements

For best results:
- Minimum 50 users with order history
- Minimum 20 menu items
- Minimum 200 completed orders
- Orders should span at least 2-3 months

With less data, the service will still work but will return more popular items than personalized recommendations.

## Production Deployment

For production, consider:

1. **Process Manager** (keep service running):
   ```bash
   # Using supervisor on Linux
   pip install supervisor
   ```

2. **Reverse Proxy** (Nginx):
   ```nginx
   location /ai-api/ {
       proxy_pass http://localhost:8000/;
   }
   ```

3. **Environment Variables**: Never commit `.env` to git

4. **Monitoring**: Use logging and health check endpoint

5. **Scaling**: Can run multiple instances with load balancer

## License

Part of The Stag SmartDine system.
