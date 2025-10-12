cd # AI Chatbot dengan Groq - Setup Guide

## Overview
Chatbot AI untuk The Stag SmartDine yang fully integrated dengan restaurant data menggunakan Groq AI API.

## Features Implemented
- ✅ Real-time chat dengan Groq AI (Llama 3 model)
- ✅ Context-aware dengan restaurant menu, orders, user history
- ✅ Session management dengan auto-timeout (30 minutes)
- ✅ Bilingual support (Bahasa Malaysia & English)
- ✅ Personalized responses berdasarkan user profile
- ✅ Modern responsive UI dengan typing indicators
- ✅ Local storage untuk session persistence

## Architecture

### Backend Components
```
app/Services/GroqChatService.php          - Groq AI integration
app/Models/ChatSession.php                 - Chat session model
app/Models/ChatMessage.php                 - Chat message model
app/Http/Controllers/Api/ChatController.php - API endpoints
database/migrations/*_create_chat_*.php    - Database schema
```

### Frontend Components
```
resources/views/partials/chatbot.blade.php - Chat widget UI & JavaScript
```

### API Endpoints
```
POST /api/chatbot/start    - Start new chat session
POST /api/chatbot/send     - Send message & get AI response
POST /api/chatbot/history  - Get chat history
POST /api/chatbot/end      - End chat session
GET  /api/chatbot/health   - Health check
```

## Installation Steps

### 1. Get Groq API Key
1. Visit [https://console.groq.com](https://console.groq.com)
2. Sign up atau login
3. Navigate to API Keys section
4. Create new API key
5. Copy API key (starts with `gsk_...`)

### 2. Configure Environment Variables
Tambah ke `.env` file:
```bash
# Groq AI Chatbot Configuration
GROQ_API_KEY=your_groq_api_key_here
GROQ_BASE_URL=https://api.groq.com/openai/v1
GROQ_MODEL=llama3-8b-8192
GROQ_TIMEOUT=30
```

**Available Models:**
- `llama3-8b-8192` - Fast, recommended (default)
- `llama3-70b-8192` - More powerful, slower
- `mixtral-8x7b-32768` - Larger context window
- `gemma-7b-it` - Google's Gemma model

### 3. Run Database Migrations
```bash
php artisan migrate
```

Ini akan create 2 tables:
- `chat_sessions` - Store chat sessions
- `chat_messages` - Store all messages

### 4. Include Chatbot Widget
Tambah chatbot widget di layout file (contoh: `resources/views/layouts/customer.blade.php`):

```php
@include('partials.chatbot')
```

**Recommended locations:**
- Customer layout: `layouts/customer.blade.php`
- QR menu layout: `layouts/qr.blade.php`
- Admin layout (optional): `layouts/admin.blade.php`

### 5. Clear Cache
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
```

## Testing

### 1. Test Groq Connection
```bash
php artisan tinker
```

```php
$service = app(\App\Services\GroqChatService::class);
$service->healthCheck(); // Should return true
```

### 2. Test Chatbot in Browser
1. Navigate ke any customer page
2. Click floating chat button (bottom right)
3. Chat should start automatically with welcome message
4. Try these test messages:
   - "Apa menu yang ada?"
   - "Ada makanan vegetarian tak?"
   - "Recommend something spicy"
   - "What are your operating hours?"

### 3. Test API Endpoints
```bash
# Start session
curl -X POST http://localhost/api/chatbot/start \
  -H "Content-Type: application/json"

# Send message
curl -X POST http://localhost/api/chatbot/send \
  -H "Content-Type: application/json" \
  -d '{"session_token":"YOUR_TOKEN","message":"Hello"}'

# Health check
curl http://localhost/api/chatbot/health
```

## Restaurant Context Integration

Chatbot automatically includes:

### 1. Menu Items
- Real-time menu availability
- Prices, descriptions, categories
- Featured items highlighted
- Allergen information

### 2. User Profile (bila logged in)
- User name untuk personalization
- Previous order history
- Favorite categories
- Dietary preferences

### 3. Current Cart
- Items in cart
- Quantities and prices
- Special notes

### 4. Restaurant Info
- Operating hours
- Service types (dine-in, takeaway, QR)
- Payment methods
- Current promotions

## Customization

### Change AI Model
Edit `.env`:
```bash
GROQ_MODEL=llama3-70b-8192  # For better responses
```

### Adjust Response Style
Edit [GroqChatService.php:96](app/Services/GroqChatService.php#L96) method `buildSystemPrompt()`:
```php
$prompt = "You are The Stag SmartDine AI Assistant...";
// Customize instructions here
```

### Change UI Colors
Edit [chatbot.blade.php:41](resources/views/partials/chatbot.blade.php#L41):
```css
.chatbot-toggle {
    background: linear-gradient(135deg, #YOUR_COLOR, #YOUR_COLOR);
}
```

### Session Timeout
Edit [ChatSession.php:90](app/Models/ChatSession.php#L90):
```php
if ($this->last_activity_at->diffInMinutes(now()) > 30) { // Change 30 to your value
```

## Troubleshooting

### Issue: "Groq service unavailable"
**Solution:**
1. Check API key di `.env` is correct
2. Run `php artisan config:clear`
3. Test connection: `$service->healthCheck()`
4. Check Groq API status at [https://status.groq.com](https://status.groq.com)

### Issue: Chat window not showing
**Solution:**
1. Ensure `@include('partials.chatbot')` ada dalam layout
2. Check browser console for JavaScript errors
3. Clear browser cache

### Issue: "Session timeout" immediately
**Solution:**
1. Check database connection
2. Ensure migrations ran successfully
3. Check `chat_sessions` table exists

### Issue: AI responses are slow
**Solution:**
1. Use faster model: `llama3-8b-8192`
2. Reduce context size in `buildRestaurantContext()`
3. Increase timeout: `GROQ_TIMEOUT=60`

### Issue: AI gives generic responses
**Solution:**
1. Check menu items are in database
2. Verify context is being built properly
3. Customize system prompt to be more specific

## Database Schema

### chat_sessions
```sql
- id: bigint (PK)
- user_id: bigint (FK to users) nullable
- session_token: varchar(255) unique
- user_ip: varchar(255) nullable
- user_agent: varchar(255) nullable
- status: enum('active','ended','timeout')
- last_activity_at: timestamp
- created_at, updated_at, deleted_at
```

### chat_messages
```sql
- id: bigint (PK)
- chat_session_id: bigint (FK to chat_sessions)
- role: enum('user','assistant','system')
- message: text
- context_data: json nullable
- metadata: json nullable
- created_at, updated_at, deleted_at
```

## Performance Optimization

### 1. Database Indexing
Already optimized dengan indexes pada:
- `session_token`, `user_id`, `status` in chat_sessions
- `chat_session_id`, `role`, `created_at` in chat_messages

### 2. Context Size Management
Limit menu items to top 30 most relevant:
```php
->limit(30) // Adjust in GroqChatService
```

### 3. Chat History Limit
Last 20 messages kept in context:
```php
->take(20) // Adjust in ChatController
```

### 4. Caching (Optional)
Consider caching menu data:
```php
Cache::remember('chatbot_menu_context', 300, function() {
    return $this->getAvailableMenuItems();
});
```

## Security Considerations

1. **API Key Protection**: Never commit `.env` file
2. **Rate Limiting**: Add to routes if needed
3. **Input Validation**: Already implemented in controller
4. **Session Security**: Auto-timeout after 30 minutes
5. **Guest Users**: Supported but limited context

## Cost Estimation (Groq)

Groq is currently **FREE** for:
- 30 requests per minute
- 14,400 tokens per minute

Perfect for restaurant chatbot use case!

## Future Enhancements

Possible additions:
- [ ] Voice input/output
- [ ] Image recognition for menu items
- [ ] Order placement through chat
- [ ] Table reservation through chat
- [ ] Multi-language support (Chinese, Tamil)
- [ ] Sentiment analysis
- [ ] Chat analytics dashboard
- [ ] Export chat transcripts

## Support

For issues or questions:
1. Check error logs: `storage/logs/laravel.log`
2. Review Groq documentation: [https://console.groq.com/docs](https://console.groq.com/docs)
3. Check chatbot health: `/api/chatbot/health`

## Credits

- **AI Model**: Groq (Llama 3)
- **Framework**: Laravel 10
- **Integration**: Custom implementation for The Stag SmartDine

---

**Last Updated**: 2025-10-10
**Version**: 1.0.0
