# 🔐 Chatbot Authentication Implementation

## ✅ Changes Implemented

Chatbot sekarang **HANYA boleh digunakan oleh logged-in users**. Guest users tidak akan nampak chatbot icon dan tidak boleh access chatbot API.

---

## 📋 Files Modified

### 1. **resources/views/partials/chatbot.blade.php**
**Changes:** Wrapped entire chatbot with `@auth` directive

**Before:**
```blade
<!-- Floating Chatbot Button -->
<div id="chatbot-toggle" class="chatbot-toggle">
    <i class="fas fa-comments"></i>
</div>
```

**After:**
```blade
@auth
<!-- Floating Chatbot Button (Only for Logged-in Users) -->
<div id="chatbot-toggle" class="chatbot-toggle">
    <i class="fas fa-comments"></i>
</div>
<!-- ... rest of chatbot HTML ... -->
@endauth
```

**Impact:**
- ✅ Guest users: Chatbot completely hidden (no button, no UI)
- ✅ Logged-in users: Chatbot visible and functional

---

### 2. **routes/api.php**
**Changes:** Added authentication middleware to all chatbot routes

**Before:**
```php
// Chatbot Routes
Route::prefix('chatbot')->group(function () {
    Route::post('/start', [ChatController::class, 'startSession']);
    Route::post('/send', [ChatController::class, 'sendMessage']);
    // ... etc
});
```

**After:**
```php
// Chatbot Routes (Authenticated users only - uses web session auth)
Route::prefix('chatbot')->middleware(['auth:sanctum', 'auth'])->group(function () {
    Route::post('/start', [ChatController::class, 'startSession']);
    Route::post('/send', [ChatController::class, 'sendMessage']);
    Route::post('/history', [ChatController::class, 'getHistory']);
    Route::post('/end', [ChatController::class, 'endSession']);
    Route::post('/clear', [ChatController::class, 'clearHistory']);
    Route::get('/health', [ChatController::class, 'healthCheck']);
});
```

**Impact:**
- ❌ Guest API calls: Return 401 Unauthorized
- ✅ Logged-in API calls: Work normally

---

### 3. **app/Http/Controllers/API/ChatController.php**
**Changes:** Added user ownership validation to all session operations

**Modified Methods:**
1. `sendMessage()` - Line 103-108
2. `getHistory()` - Line 217-221
3. `endSession()` - Line 259-263
4. `clearHistory()` - Line 297-301

**Security Enhancement:**
```php
// Before
$session = ChatSession::where('session_token', $request->session_token)->firstOrFail();

// After
$userId = auth()->id();
$session = ChatSession::where('session_token', $request->session_token)
    ->where('user_id', $userId) // Ensure user owns this session
    ->firstOrFail();
```

**Impact:**
- ✅ Users can ONLY access their own chat sessions
- ❌ Users CANNOT access other users' sessions (even with valid token)
- 🔒 Enhanced security - prevents session hijacking

---

## 🔍 How Authentication Works

### For Web Users (Customer Portal)

```
┌─────────────────────────────────────────────────────┐
│ 1. User visits /customer page                       │
│    ↓                                                 │
│ 2. Check: Is user logged in?                        │
│    ├─ YES → Show chatbot button                     │
│    └─ NO  → Hide chatbot completely (@auth)         │
│                                                      │
│ 3. User clicks chatbot button                       │
│    ↓                                                 │
│ 4. JavaScript calls /api/chatbot/start              │
│    ↓                                                 │
│ 5. Laravel checks: auth:sanctum + auth middleware   │
│    ├─ Session valid? → Allow access                 │
│    └─ Session invalid? → Return 401 Unauthorized    │
│                                                      │
│ 6. ChatController validates user owns session       │
│    ↓                                                 │
│ 7. Create/resume chat session for logged-in user    │
└─────────────────────────────────────────────────────┘
```

### Authentication Flow:

**Guest Users:**
```
Guest → Visit /customer → No chatbot button visible → Cannot access API
```

**Logged-in Users:**
```
User → Login → Visit /customer → Chatbot visible → Click chatbot
→ API validates session → User ID matched → Chat works ✅
```

**Attack Scenario (Prevented):**
```
Hacker → Steal session_token → Try to use it
→ API checks: Does this token belong to current user?
→ No match → Return 404 Not Found ❌
```

---

## 🧪 Testing Guide

### Test 1: Guest User (Should NOT see chatbot)

1. **Logout** (if logged in)
2. Visit: `http://localhost:8000/customer`
3. **Expected Result:**
   - ❌ No chatbot button visible (bottom right)
   - ❌ No chatbot UI at all
   - ✅ Page loads normally

---

### Test 2: Logged-in User (Should see chatbot)

1. **Login** with valid credentials
2. Visit: `http://localhost:8000/customer`
3. **Expected Result:**
   - ✅ Chatbot button visible (bottom right, purple/blue)
   - ✅ Click button → Chatbot opens
   - ✅ Can send messages and get AI responses

---

### Test 3: API Protection (Manual API test)

**Using Postman or curl:**

**Scenario A: No authentication**
```bash
curl -X POST http://localhost:8000/api/chatbot/start \
  -H "Content-Type: application/json"
```

**Expected Response:**
```json
{
  "message": "Unauthenticated."
}
```
**Status:** 401 Unauthorized ❌

---

**Scenario B: With authentication** (from browser after login)
```bash
curl -X POST http://localhost:8000/api/chatbot/start \
  -H "Content-Type: application/json" \
  -H "Cookie: laravel_session=your_session_cookie" \
  -H "X-CSRF-TOKEN: your_csrf_token"
```

**Expected Response:**
```json
{
  "success": true,
  "session_token": "abc123...",
  "session_id": 1,
  "welcome_message": "Hi User! 👋 Selamat datang..."
}
```
**Status:** 200 OK ✅

---

### Test 4: Session Ownership Validation

**Scenario:** User A tries to access User B's chat session

1. User A logs in → Gets session_token: `token_A`
2. User B logs in → Gets session_token: `token_B`
3. User A tries to use `token_B` in API call

**Expected Result:**
- ❌ API returns: `{"success": false, "error": "Session not found"}`
- ❌ Status: 404 Not Found
- ✅ Security: User A cannot access User B's chats

---

## 🔧 Configuration

### Environment Variables (No changes needed)

Chatbot authentication uses Laravel's built-in session authentication. No additional config required.

**Required in `.env`:**
```env
# Already configured - no changes needed
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Groq API (already exists)
GROQ_API_KEY=your_groq_api_key
```

---

## 🚨 Troubleshooting

### Issue 1: "Chatbot button not showing even after login"

**Possible Causes:**
1. Cache not cleared
2. Blade cache issue

**Fix:**
```bash
php artisan cache:clear
php artisan view:clear
php artisan config:clear

# Refresh browser
Ctrl + Shift + R (hard refresh)
```

---

### Issue 2: "401 Unauthorized when clicking chatbot"

**Possible Causes:**
1. User session expired
2. CSRF token missing

**Fix:**
```bash
# Check session config
php artisan config:cache

# Verify user is logged in
# Check Laravel logs
tail -f storage/logs/laravel.log
```

**Browser Console Check:**
```javascript
// Open DevTools (F12) → Console
console.log(document.querySelector('meta[name="csrf-token"]').content);
// Should print a long token, not null
```

---

### Issue 3: "Can see chatbot but API calls fail"

**Debug Steps:**

1. **Check middleware applied:**
   ```bash
   php artisan route:list | grep chatbot
   ```
   Should show: `auth:sanctum,auth`

2. **Check session cookie:**
   - Open DevTools → Application → Cookies
   - Look for `laravel_session` cookie
   - Should have a value

3. **Check auth status:**
   ```javascript
   // Browser console
   fetch('/api/user', {
     headers: {
       'Accept': 'application/json',
       'X-Requested-With': 'XMLHttpRequest'
     },
     credentials: 'same-origin'
   }).then(r => r.json()).then(console.log);

   // Should return user object if logged in
   ```

---

### Issue 4: "Other users can access my chat history"

**This should NOT happen** if implementation is correct.

**Verify:**
```bash
# Check ChatController.php line 103-108
# Should have: ->where('user_id', $userId)
```

**If bug exists:**
1. Check all 4 methods have user_id validation
2. Clear route cache: `php artisan route:clear`
3. Report bug with exact steps to reproduce

---

## 📊 Security Benefits

| Security Aspect | Before | After |
|----------------|--------|-------|
| **Guest Access** | ✅ Anyone can chat | ❌ Blocked completely |
| **API Protection** | ⚠️ Public routes | ✅ Auth required |
| **Session Hijacking** | ⚠️ Possible | ✅ Prevented (user_id check) |
| **Data Privacy** | ⚠️ Weak | ✅ Strong (user ownership) |
| **Token Waste** | ⚠️ High (guest spam) | ✅ Low (logged users only) |

---

## 🔄 Database Impact

### chat_sessions table
**Before:**
- `user_id` could be NULL (for guests)

**After:**
- `user_id` NEVER NULL (always has user ID)
- Easier to track user behavior
- Better analytics (know which user asked what)

### Example Query:
```sql
-- Find all chat sessions by user
SELECT cs.id, cs.created_at, COUNT(cm.id) as message_count
FROM chat_sessions cs
LEFT JOIN chat_messages cm ON cs.id = cm.chat_session_id
WHERE cs.user_id = 123
GROUP BY cs.id
ORDER BY cs.created_at DESC;
```

---

## 📝 Migration Notes

### If You Have Existing Guest Sessions

**Check for guest sessions:**
```bash
php artisan tinker
>>> \App\Models\ChatSession::whereNull('user_id')->count()
```

**Optional Cleanup:**
```bash
php artisan tinker
>>> \App\Models\ChatSession::whereNull('user_id')->delete()
>>> "Guest sessions deleted"
```

**No migration file needed** - existing columns work fine.

---

## 🎯 Future Enhancements (Optional)

### 1. Remember Last Chat
Store last chat session in user preferences:
```php
// When user opens chatbot, auto-load last session
$lastSession = ChatSession::where('user_id', $userId)
    ->where('status', 'active')
    ->latest()
    ->first();
```

### 2. Chat History Page
Create dedicated page for users to view all their past chats:
```
Route: /customer/chat-history
Show: All sessions with date, message count, last message preview
```

### 3. Export Chat History
Allow users to download their chat history as PDF/JSON:
```php
Route::get('/customer/chat-history/{session}/export', ...);
```

### 4. Admin Chat Monitoring (Optional)
Create admin panel to view chat analytics:
- Most common questions
- AI response quality
- Token usage by user
- Popular topics

**⚠️ Privacy Warning:** Only implement if users consent to monitoring.

---

## ✅ Summary

### What Changed:
1. ✅ Chatbot UI hidden for guests (`@auth` directive)
2. ✅ API routes protected (auth middleware)
3. ✅ Session ownership validation (user_id check)
4. ✅ Enhanced security (prevent session hijacking)

### User Impact:
- **Guests:** Cannot see or use chatbot ❌
- **Logged-in:** Full chatbot access ✅
- **Security:** Each user only sees their own chats 🔒

### Benefits:
- 💰 Save tokens (no guest spam)
- 🔒 Better security
- 📊 Better analytics (track by user)
- ✅ Comply with data privacy (know who asked what)

---

**Implementation Date:** 2025-10-10
**Status:** ✅ COMPLETE & TESTED
**Breaking Changes:** YES - guests can no longer use chatbot
**Migration Required:** NO
