# AI Recommendation System - Complete Guide

## System Overview

The AI Recommendation System uses a hybrid approach combining **Collaborative Filtering (CF)** and **Content-Based Filtering (CBF)** to provide personalized menu recommendations to users.

### Key Features
- ✅ **Collaborative Filtering**: User-to-user similarity based recommendations using SVD (Singular Value Decomposition)
- ✅ **Content-Based Filtering**: Item similarity using TF-IDF and cosine similarity
- ✅ **Quantity-Weighted Scoring**: Orders and cart items weighted by quantity
- ✅ **Real-time Database Integration**: Direct MySQL connection for live data
- ✅ **Automatic Model Retraining**: Scheduled daily updates
- ✅ **Graceful Fallback**: Rule-based recommendations when AI unavailable

---

## Architecture

### Technology Stack
- **Laravel 10**: Main application framework
- **Python FastAPI**: AI recommendation service
- **MySQL**: Shared database for both Laravel and Python
- **Docker**: Containerized Python service
- **Surprise Library**: Collaborative filtering (SVD)
- **Scikit-learn**: Content-based filtering (TF-IDF, Nearest Neighbors)

### Data Flow

```
┌─────────────────────────────────────────────────────────────┐
│                    STARTUP (One-time)                       │
│  1. Load training data from MySQL (orders + carts)          │
│  2. Train SVD model for collaborative filtering             │
│  3. Build TF-IDF matrix for content-based filtering         │
│  4. Calculate user-user and item-item similarities          │
└─────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────┐
│              PREDICTION (Every request)                      │
│  1. Get real-time user context (cart, recent orders)        │
│  2. Get available menu items (real-time)                    │
│  3. Calculate CF scores (using trained matrix)              │
│  4. Calculate CBF scores (using TF-IDF + context)           │
│  5. Blend scores using adaptive alpha                       │
│  6. Return top N recommendations                            │
└─────────────────────────────────────────────────────────────┘
                              ↓
┌─────────────────────────────────────────────────────────────┐
│                AUTO-RETRAIN (Daily 3:00 AM)                 │
│  1. Fetch latest data from MySQL                            │
│  2. Retrain SVD model with new interactions                 │
│  3. Update user similarities                                │
│  4. Model stays current with user behavior                  │
└─────────────────────────────────────────────────────────────┘
```

---

## Quantity-Weighted Scoring

### The Problem
Previously, the system treated all orders equally:
- User orders 1 Teh Ais → score = 4.0
- User orders 10 Nasi Lemak → score = 4.0

This didn't reflect user preference strength.

### The Solution
Implemented logarithmic quantity weighting:

```python
# For orders
quantity_weight = log(1 + quantity)
score = 4.0 × quantity_weight

# For cart items
quantity_weight = log(1 + quantity)
score = 3.0 × quantity_weight
```

### Scoring Examples
| Quantity | Weight | Order Score | Cart Score |
|----------|--------|-------------|------------|
| 1        | 1.00   | 4.00        | 3.00       |
| 2        | 1.41   | 5.64        | 4.23       |
| 3        | 1.58   | 6.32        | 4.74       |
| 5        | 1.79   | 7.16        | 5.37       |
| 10       | 2.40   | 9.59        | 7.19       |

**Why logarithmic?**
- Prevents extreme values (quantity 100 won't dominate)
- Still gives higher preference to larger quantities
- Smooth diminishing returns (10→11 has less impact than 1→2)

---

## Real-time vs Static Data

### Static Data (Updated on retrain)
- ❌ User-item interaction matrix
- ❌ CF scores and user similarities
- ❌ SVD latent factors

**When updated**: Daily at 3:00 AM via scheduler

### Real-time Data (Every request)
- ✅ Available menu items
- ✅ User current cart
- ✅ Recent orders (last 30 days)
- ✅ User rating patterns
- ✅ Menu item availability

---

## Automatic Retraining System

### Schedule Configuration
The AI model automatically retrains daily at **3:00 AM** via Laravel scheduler:

```php
// app/Console/Kernel.php
$schedule->command('ai:retrain')
    ->dailyAt('03:00')
    ->withoutOverlapping()
    ->onSuccess(function () {
        \Log::info('AI recommendation model retrained successfully');
    })
    ->onFailure(function () {
        \Log::error('AI recommendation model retrain failed');
    });
```

### Manual Retrain
You can manually trigger retraining:

```bash
# Force retrain with health check
php artisan ai:retrain

# Force retrain even if service unhealthy
php artisan ai:retrain --force

# Get JSON output
php artisan ai:retrain --json
```

### Retrain Triggers
Model retrains automatically in these scenarios:

1. **Scheduled**: Daily at 3:00 AM
2. **Order completion**: Background job after order completed
3. **Menu updates**: Background job after menu item changes
4. **Manual**: Admin can trigger via artisan command

---

## API Endpoints

### Get Recommendations
```http
POST http://localhost:8000/recommend
Content-Type: application/json

{
  "user_id": 1,
  "topn": 10,
  "exclude_items": [5, 12]
}
```

**Response:**
```json
{
  "user_id": 1,
  "recommendations": [
    {
      "menu_id": 23,
      "name": "Nasi Lemak Special",
      "score": 0.87
    },
    {
      "menu_id": 15,
      "name": "Teh Tarik",
      "score": 0.82
    }
  ],
  "method": "hybrid",
  "alpha": 0.45,
  "total_count": 10
}
```

### Retrain Model
```http
POST http://localhost:8000/retrain
```

**Response:**
```json
{
  "status": "success",
  "message": "Model retrained successfully",
  "records_used": 1523,
  "training_time": "3.2s"
}
```

### Health Check
```http
GET http://localhost:8000/health
```

### Model Status
```http
GET http://localhost:8000/model/status
```

---

## Laravel Integration

### Service Usage

```php
use App\Services\RecommendationService;

$recommendationService = app(RecommendationService::class);

// Get recommendations
$recommendations = $recommendationService->getRecommendations(
    userId: 1,
    limit: 10,
    excludeItems: [5, 12]
);

// Get recommendations with scores
$detailed = $recommendationService->getRecommendationsWithScores(
    userId: 1,
    limit: 10,
    excludeItems: []
);

// Check service health
$isHealthy = $recommendationService->healthCheck();

// Get service status
$status = $recommendationService->getServiceStatus();

// Force retrain
$result = $recommendationService->forceRetrain();
```

### Fallback Strategy
The system has intelligent fallback:

1. **Primary**: AI service (hybrid CF + CBF)
2. **Fallback**: Simple rule-based recommendations
3. **Last resort**: Popular items

```php
// In RecommendationService.php
public function getRecommendations(int $userId, int $limit = 10, ?array $excludeItems = null): array
{
    $aiEnabled = config('services.ai_recommender.enabled', true);

    if ($aiEnabled) {
        try {
            // Try AI service
            $response = Http::timeout($this->timeout)
                ->post("{$this->baseUrl}/recommend", $requestData);

            if ($response->successful()) {
                return $aiRecommendations;
            }
        } catch (Exception $e) {
            Log::warning('AI service unavailable, falling back to smart rules');
        }
    }

    // Fallback to simple recommender
    return $this->simpleRecommender->getRecommendations($userId, $limit, $excludeItems);
}
```

---

## Configuration

### Environment Variables
```bash
# .env
AI_RECOMMENDER_BASE_URL=http://localhost:8000
AI_RECOMMENDER_TIMEOUT=30
AI_RECOMMENDER_ENABLED=true
```

### Config File
```php
// config/services.php
'ai_recommender' => [
    'enabled' => env('AI_RECOMMENDER_ENABLED', true),
    'base_url' => env('AI_RECOMMENDER_BASE_URL', 'http://localhost:8000'),
    'timeout' => env('AI_RECOMMENDER_TIMEOUT', 30),
],
```

---

## Docker Setup

### Start Python AI Service
```bash
# Start the service
docker-compose up -d

# View logs
docker-compose logs -f ai_recommender

# Rebuild after code changes
docker-compose up -d --build
```

### Docker Compose Configuration
```yaml
services:
  ai_recommender:
    build: ./ai_recommend
    ports:
      - "8000:8000"
    environment:
      - DB_HOST=host.docker.internal
      - DB_PORT=3306
      - DB_DATABASE=the_stag
      - DB_USERNAME=root
      - DB_PASSWORD=
    volumes:
      - ./ai_recommend:/app
```

---

## Troubleshooting

### Issue: Only 5 items returned instead of requested amount
**Cause**: Parameter mismatch - Laravel sent `limit` but Python expected `topn`

**Fix**: Updated RecommendationService.php:
```php
$requestData = [
    'user_id' => $userId,
    'topn' => $limit,  // Changed from 'limit'
];
```

### Issue: AI service not responding
**Check**:
1. Docker container running: `docker ps`
2. Service health: `curl http://localhost:8000/health`
3. Logs: `docker-compose logs ai_recommender`
4. Laravel will automatically fallback to rule-based recommendations

### Issue: Recommendations not updating
**Solution**: Trigger manual retrain:
```bash
php artisan ai:retrain
```

### Issue: Model not using latest orders
**Check**: Verify scheduler is running:
```bash
# Run scheduler manually
php artisan schedule:run

# Check next run time
php artisan schedule:list
```

---

## Monitoring & Logs

### Laravel Logs
```bash
tail -f storage/logs/laravel.log | grep "recommendation"
```

**Key log entries:**
- `AI recommendations successful`
- `AI service unavailable, falling back to smart rules`
- `Using smart rule-based recommendations`
- `AI recommendation model retrained successfully`

### Python Service Logs
```bash
docker-compose logs -f ai_recommender
```

### Check Retrain History
```bash
# View scheduled task logs
grep "AI recommendation model" storage/logs/laravel.log
```

---

## Performance

### Typical Response Times
- **AI recommendation**: 100-300ms
- **Fallback (rule-based)**: 50-100ms
- **Model retraining**: 2-5 seconds (for 1000-5000 records)

### Optimization Tips
1. **Enable caching** for frequently requested recommendations
2. **Exclude items** already in cart to reduce computation
3. **Limit topn** to reasonable number (10-20)
4. **Use JSON output** for faster parsing in APIs

---

## Testing

### Test AI Service
```bash
# Test from Laravel
php artisan tinker
>>> app(\App\Services\RecommendationService::class)->healthCheck()
>>> app(\App\Services\RecommendationService::class)->getRecommendations(1, 10)
```

### Test Quantity Weighting
```bash
cd ai_recommend
python test_quantity_scoring.py
```

### Test Retrain
```bash
php artisan ai:retrain
```

---

## Future Improvements

### Potential Enhancements
- [ ] Real-time CF matrix updates (streaming)
- [ ] A/B testing framework for recommendation algorithms
- [ ] Contextual recommendations (time of day, weather)
- [ ] Multi-armed bandit for exploration vs exploitation
- [ ] Deep learning models for complex patterns

### Monitoring Dashboard
- [ ] Admin panel showing model performance
- [ ] Recommendation acceptance rate
- [ ] User engagement metrics
- [ ] Model drift detection

---

## Related Files

### Laravel
- `app/Services/RecommendationService.php` - Main service class
- `app/Services/SimpleRecommendationService.php` - Fallback rules
- `app/Console/Commands/AiRetrain.php` - Manual retrain command
- `app/Console/Kernel.php` - Scheduler configuration
- `config/services.php` - AI service configuration

### Python AI Service
- `ai_recommend/main.py` - FastAPI endpoints
- `ai_recommend/recommender.py` - Hybrid recommendation logic
- `ai_recommend/data_loader.py` - Quantity-weighted scoring
- `ai_recommend/database.py` - Database connection
- `ai_recommend/training.py` - SVD model training
- `ai_recommend/test_quantity_scoring.py` - Test script

---

## Support

For issues or questions:
1. Check logs: `storage/logs/laravel.log`
2. Check Python logs: `docker-compose logs ai_recommender`
3. Test health: `php artisan ai:retrain --force`
4. Review this documentation

---

**Last Updated**: 2025-11-08
**System Version**: 1.0
**Status**: ✅ Fully Operational with Quantity Weighting and Auto-Retrain
