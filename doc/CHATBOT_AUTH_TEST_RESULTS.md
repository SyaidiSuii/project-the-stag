# 🧪 Chatbot Authentication - Testing Checklist

## ✅ Implementation Complete

Chatbot sekarang **restricted to logged-in users only**.

---

## 📋 Manual Testing Checklist

### Test 1: Guest User Cannot See Chatbot ❌

**Steps:**
1. Logout dari system (atau open incognito browser)
2. Navigate to: `http://localhost:8000/customer`
3. Look for chatbot button (bottom right corner)

**Expected Result:**
- [ ] ❌ NO chatbot button visible
- [ ] ❌ NO chatbot UI elements
- [ ] ✅ Page loads normally without errors

**Status:** 🔲 NOT TESTED YET

---

### Test 2: Logged-in User Can See Chatbot ✅

**Steps:**
1. Login to system
   - Email: (your test user)
   - Password: (your password)
2. Navigate to: `http://localhost:8000/customer`
3. Look for chatbot button (bottom right corner)

**Expected Result:**
- [ ] ✅ Chatbot button IS visible (purple/blue floating button)
- [ ] ✅ Click button → Chatbot window opens
- [ ] ✅ Welcome screen shows with user's name
- [ ] ✅ Can type message and get response

**Status:** 🔲 NOT TESTED YET

---

### Test 3: Guest Cannot Access API ❌

**Steps:**
1. Logout from system
2. Open Browser DevTools (F12)
3. Go to Console tab
4. Run this command:
```javascript
fetch('/api/chatbot/start', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
}).then(r => r.json()).then(console.log);
```

**Expected Result:**
- [ ] ❌ Response: `{"message": "Unauthenticated."}`
- [ ] ❌ Status: 401 Unauthorized
- [ ] ❌ No session created

**Status:** 🔲 NOT TESTED YET

---

### Test 4: Logged-in User Can Access API ✅

**Steps:**
1. Login to system
2. Open Browser DevTools (F12)
3. Go to Console tab
4. Get CSRF token:
```javascript
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
console.log('CSRF:', csrfToken);
```
5. Test API:
```javascript
fetch('/api/chatbot/start', {
  method: 'POST',
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json',
    'X-CSRF-TOKEN': csrfToken,
    'X-Requested-With': 'XMLHttpRequest'
  },
  credentials: 'same-origin'
}).then(r => r.json()).then(console.log);
```

**Expected Result:**
- [ ] ✅ Response includes: `{"success": true, "session_token": "...", ...}`
- [ ] ✅ Status: 200 OK
- [ ] ✅ Session created successfully

**Status:** 🔲 NOT TESTED YET

---

### Test 5: User Can Send Message ✅

**Steps:**
1. Login to system
2. Open chatbot
3. Type message: "Menu apa yang ada?"
4. Press send

**Expected Result:**
- [ ] ✅ Message appears in chat (user bubble, right side)
- [ ] ✅ Typing indicator shows (3 dots)
- [ ] ✅ AI response appears (bot bubble, left side)
- [ ] ✅ Response is relevant (about restaurant menu)

**Status:** 🔲 NOT TESTED YET

---

### Test 6: Scope Filtering Works ✅

**Steps:**
1. Login to system
2. Open chatbot
3. Ask off-topic question: "Siapa perdana menteri Malaysia?"

**Expected Result:**
- [ ] ✅ AI responds with redirect message:
  ```
  Maaf, saya hanya membantu soalan tentang The Stag SmartDine restoran.
  Ada yang nak tanya pasal menu atau tempahan?
  ```
- [ ] ✅ AI does NOT answer the political question
- [ ] ✅ Response is SHORT (1-2 lines only)

**Status:** 🔲 NOT TESTED YET

---

### Test 7: Clear History Works ✅

**Steps:**
1. Login to system
2. Open chatbot
3. Send 2-3 messages
4. Click "Clear History" button (trash icon in header)
5. Confirm deletion

**Expected Result:**
- [ ] ✅ All messages disappear from chat
- [ ] ✅ Welcome screen appears again
- [ ] ✅ Chat history is empty
- [ ] ✅ Can start new conversation

**Status:** 🔲 NOT TESTED YET

---

### Test 8: Session Persistence Across Page Refresh ✅

**Steps:**
1. Login to system
2. Open chatbot
3. Send message: "Hello"
4. Get AI response
5. Refresh page (F5)
6. Open chatbot again

**Expected Result:**
- [ ] ✅ Previous messages still visible
- [ ] ✅ Chat history loaded correctly
- [ ] ✅ NO welcome screen (because history exists)
- [ ] ✅ Can continue conversation

**Status:** 🔲 NOT TESTED YET

---

### Test 9: Session Ownership Protection 🔒

**Advanced Test - Requires 2 Users**

**Steps:**
1. **User A:**
   - Login as User A
   - Open chatbot
   - Send message
   - Open DevTools → Application → LocalStorage
   - Copy `chatbot_session` token value

2. **User B:**
   - Logout User A
   - Login as User B
   - Open DevTools → Console
   - Try to use User A's token:
   ```javascript
   fetch('/api/chatbot/send', {
     method: 'POST',
     headers: {
       'Content-Type': 'application/json',
       'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
     },
     credentials: 'same-origin',
     body: JSON.stringify({
       session_token: 'USER_A_TOKEN_HERE',
       message: 'test'
     })
   }).then(r => r.json()).then(console.log);
   ```

**Expected Result:**
- [ ] ❌ Response: `{"success": false, "error": "Session not found"}`
- [ ] ❌ Status: 404 Not Found
- [ ] ✅ User B CANNOT access User A's session
- [ ] 🔒 Security: Session hijacking prevented

**Status:** 🔲 NOT TESTED YET

---

### Test 10: Multiple Tabs/Windows ✅

**Steps:**
1. Login to system
2. Open chatbot in Tab 1
3. Send message: "Test 1"
4. Open same page in Tab 2
5. Open chatbot in Tab 2
6. Send message: "Test 2"
7. Go back to Tab 1

**Expected Result:**
- [ ] ✅ Tab 1 and Tab 2 share same session (same session_token)
- [ ] ✅ Messages visible in both tabs after refresh
- [ ] ✅ No duplicate sessions created

**Status:** 🔲 NOT TESTED YET

---

## 🐛 Known Issues / Edge Cases

### Issue 1: Session Timeout
**Scenario:** User idle for 30+ minutes

**Expected Behavior:**
- Session status changes to 'timeout'
- Next message attempt shows error
- User needs to refresh page to start new session

**Test:** 🔲 NOT TESTED

---

### Issue 2: Logout While Chat Open
**Scenario:** User has chatbot open, then logs out

**Expected Behavior:**
- Chatbot button disappears after logout
- Next API call returns 401 Unauthorized
- LocalStorage token becomes invalid

**Test:** 🔲 NOT TESTED

---

### Issue 3: Concurrent Login on Different Devices
**Scenario:** User logs in on Phone and Desktop

**Expected Behavior:**
- Phone and Desktop have DIFFERENT sessions
- Each device maintains its own chat history
- No conflicts or cross-device issues

**Test:** 🔲 NOT TESTED

---

## 📊 Test Summary

| Test Case | Priority | Status | Pass/Fail | Notes |
|-----------|----------|--------|-----------|-------|
| Guest cannot see chatbot | 🔥 HIGH | 🔲 | - | Critical security |
| Logged-in can see chatbot | 🔥 HIGH | 🔲 | - | Core functionality |
| Guest API blocked | 🔥 HIGH | 🔲 | - | Security check |
| Logged-in API works | 🔥 HIGH | 🔲 | - | Core functionality |
| Send message | 🔥 HIGH | 🔲 | - | Core functionality |
| Scope filtering | ⚠️ MEDIUM | 🔲 | - | Token optimization |
| Clear history | ⚠️ MEDIUM | 🔲 | - | User feature |
| Session persistence | ⚠️ MEDIUM | 🔲 | - | UX enhancement |
| Session ownership | 🔥 HIGH | 🔲 | - | Critical security |
| Multiple tabs | 💡 LOW | 🔲 | - | Edge case |

---

## 🚀 Quick Test Commands

### Check Routes Have Auth
```bash
php artisan route:list --path=chatbot
# Should show 6 routes
```

### Check Blade File Has @auth
```bash
head -20 resources/views/partials/chatbot.blade.php | grep -i auth
# Should show @auth at line 1
```

### Check Controller Has user_id Validation
```bash
grep -n "where('user_id'" app/Http/Controllers/API/ChatController.php
# Should show 4 matches (lines ~106, 220, 262, 300)
```

### Check Database for Sessions
```bash
php artisan tinker
>>> \App\Models\ChatSession::with('user:id,name')->latest()->take(5)->get(['id','user_id','status','created_at'])
# Should show recent sessions with user_id (not NULL)
```

---

## ✅ Acceptance Criteria

Implementation is considered **COMPLETE** when:

1. ✅ All HIGH priority tests pass
2. ✅ No guest users can access chatbot (UI or API)
3. ✅ All logged-in users can use chatbot normally
4. ✅ Session ownership security works (Test 9 passes)
5. ✅ No errors in Laravel logs during testing
6. ✅ Performance remains good (< 3s response time)

---

## 📝 Test Results Log

**Tester:** _________________
**Date:** _________________
**Environment:** Local Development / Staging / Production

**Overall Status:** 🔲 PASS / ❌ FAIL

**Notes:**
```
[Write any observations, bugs found, or issues here]
```

---

**Next Steps After Testing:**
1. If all pass → Mark as production-ready ✅
2. If any fail → Document failures and fix
3. Update TODO.md if new features needed

---

**Created:** 2025-10-10
**Last Updated:** 2025-10-10
**Version:** 1.0
