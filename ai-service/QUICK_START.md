# Quick Start - AI Recommendation Service

## 5-Minute Setup

### 1. Run Setup
```bash
setup.bat
```

### 2. Configure Database
Edit `.env` with your database credentials (same as Laravel):
```env
DB_HOST=localhost
DB_DATABASE=project_the_stag
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 3. Start Service
```bash
run.bat
```

### 4. Verify from Laravel
Open new terminal:
```bash
cd ..
php artisan ai:status --detailed
```

## Expected Output

```
🤖 AI Recommendation Service Status
━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
Status: ✅ HEALTHY
Service URL: http://localhost:8000
Enabled: ✅ Yes

📊 Model Information:
Model Status: ✅ TRAINED
Last Training: 2025-10-25T10:30:00
```

## Done!

The AI service is now running and integrated with Laravel.

See `AI_RECOMMENDATION_COMPLETE_GUIDE.md` for usage examples.
