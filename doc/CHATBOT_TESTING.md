# Chatbot Scope Filtering - Testing Guide

## 🎯 Objektif Testing

Memastikan AI chatbot **HANYA** menjawab soalan berkaitan The Stag SmartDine restoran dan menolak semua topik lain untuk jimatkan token API.

---

## ✅ Test Cases - Restaurant Topics (SHOULD WORK)

Soalan-soalan ini **MESTI DIJAWAB** oleh chatbot:

### 1. Menu & Makanan
```
✓ "Menu apa yang ada?"
✓ "Ada nasi lemak ke?"
✓ "Berapa harga untuk chicken rice?"
✓ "What's your most popular dish?"
✓ "Ada vegetarian options tak?"
✓ "Recommend something spicy"
```

**Expected Response:**
- Mesra dan helpful
- Sebutkan menu (jika ada) atau beritahu menu being updated
- Format cantik dengan **bold** dan numbered lists
- Max 4-5 baris

---

### 2. Orders & Reservations
```
✓ "Macam mana nak order?"
✓ "Boleh book table untuk 4 orang?"
✓ "Nak takeaway boleh?"
✓ "Ada dine-in service?"
✓ "How to place an order?"
```

**Expected Response:**
- Explain ordering process (QR menu, online, etc.)
- Guide untuk table reservation
- Mention payment method (Toyyibpay)

---

### 3. Services & Location
```
✓ "Pukul berapa buka?"
✓ "Operating hours?"
✓ "Dekat mana restoran ni?"
✓ "Ada delivery service?"
✓ "Pembayaran guna apa?"
```

**Expected Response:**
- Mention operating hours (Mon-Sat 9am-11pm, Sun 10am-9pm)
- Services: Dine-in, Takeaway, QR ordering
- Payment: Online via Toyyibpay

---

## ❌ Test Cases - Off-Topic Questions (SHOULD BE REJECTED)

Soalan-soalan ini **MESTI DITOLAK** dengan polite redirect:

### 1. Politics
```
✗ "Siapa perdana menteri Malaysia?"
✗ "What do you think about the election?"
✗ "Pendapat kau pasal politik?"
```

---

### 2. Sports
```
✗ "Siapa menang EPL semalam?"
✗ "Who won the World Cup?"
✗ "Apa score Liverpool?"
```

---

### 3. Weather & News
```
✗ "Cuaca hari ni macam mana?"
✗ "What's the weather today?"
✗ "Ada berita terkini?"
```

---

### 4. Science & Math
```
✗ "What is photosynthesis?"
✗ "Calculate 25 x 34"
✗ "Explain quantum physics"
```

---

### 5. Coding & Tech
```
✗ "How to code in Python?"
✗ "What is Laravel?"
✗ "Debug this error"
```

---

### 6. Personal Advice
```
✗ "How to get rich?"
✗ "Relationship advice please"
✗ "Career guidance"
```

---

### 7. General Knowledge
```
✗ "Who is the president of USA?"
✗ "What is the capital of France?"
✗ "When did WW2 end?"
```

---

## 📋 Expected Response for Off-Topic Questions

Chatbot **MESTI** respond dengan template ini (exact match):

```
Maaf, saya hanya membantu soalan tentang The Stag SmartDine restoran. Ada yang nak tanya pasal menu atau tempahan?
```

**CRITICAL RULES:**
- ❌ DO NOT answer the off-topic question
- ❌ DO NOT elaborate or explain why
- ✅ SHORT polite redirect only
- ✅ Suggest restaurant topics instead

---

## 🧪 How to Test

### Step 1: Prepare Environment
```bash
# Clear cache
php artisan cache:clear

# Restart server if needed
php artisan serve
```

### Step 2: Open Chatbot
1. Navigate to customer page: `http://localhost:8000/customer`
2. Click chatbot icon (bottom right)
3. Clear browser cache (Ctrl+Shift+Delete)
4. Refresh page

### Step 3: Test Restaurant Questions
1. Click "Getting Started" button
2. Try questions from "✅ Restaurant Topics" section above
3. **Verify:**
   - Response is helpful and on-topic
   - Formatting looks good (**bold**, lists, line breaks)
   - Response is SHORT (4-5 lines max)
   - Language matches question (BM/English)

### Step 4: Test Off-Topic Questions
1. Ask questions from "❌ Off-Topic Questions" section
2. **Verify:**
   - Response matches exact template:
     ```
     Maaf, saya hanya membantu soalan tentang The Stag SmartDine restoran. Ada yang nak tanya pasal menu atau tempahan?
     ```
   - AI does NOT answer the question
   - AI does NOT elaborate
   - Response is SHORT (1 line only)

### Step 5: Monitor Token Usage
1. Check Laravel logs: `storage/logs/laravel.log`
2. Look for Groq API usage data
3. **Before optimization:** ~800-1200 tokens per off-topic question
4. **After optimization:** ~200-400 tokens per off-topic question (60-70% reduction!)

---

## 🐛 Common Issues & Solutions

### Issue 1: Chatbot Still Answers Off-Topic Questions
**Cause:** Cache not cleared or old session

**Fix:**
```bash
# Clear all cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Clear browser localStorage
# Open DevTools (F12) → Console → Run:
localStorage.clear()
location.reload()
```

---

### Issue 2: Response Too Long
**Cause:** AI ignoring "SHORT" instruction

**Fix:** Update `max_tokens` in [GroqChatService.php](app/Services/GroqChatService.php#L43):
```php
'max_tokens' => 300, // Reduce from 500 to 300
```

---

### Issue 3: Wrong Language Response
**Cause:** AI not detecting user language

**Fix:** Test with more explicit language:
- Bahasa: "Boleh tolong saya pasal menu?"
- English: "Can you help me with the menu?"

---

### Issue 4: "Loading..." Stuck
**Cause:** Groq API timeout or error

**Check:**
1. `.env` has valid `GROQ_API_KEY`
2. Internet connection working
3. Laravel logs for errors:
   ```bash
   tail -f storage/logs/laravel.log
   ```

---

## 📊 Success Metrics

After testing, verify:

✅ **Accuracy:** 100% restaurant questions answered correctly
✅ **Rejection:** 100% off-topic questions rejected with template
✅ **Token Savings:** 60-70% reduction for off-topic queries
✅ **Response Time:** < 3 seconds average
✅ **User Experience:** Short, helpful, formatted responses

---

## 🎯 Next Steps After Testing

If all tests pass:
1. ✅ Mark testing todo as complete
2. ✅ Add sample menu data to database (optional - untuk demo yang lebih realistic)
3. ✅ Monitor production usage and token costs

If tests fail:
1. ❌ Document which test failed
2. ❌ Check Laravel logs for errors
3. ❌ Report issue with exact error message

---

## 📝 Test Results Template

Copy this template dan fill in results:

```markdown
## Test Results - [Date]

### ✅ Restaurant Topics Test
- Menu questions: PASS / FAIL
- Orders questions: PASS / FAIL
- Services questions: PASS / FAIL
- Response format: PASS / FAIL
- Response length: PASS / FAIL

### ❌ Off-Topic Test
- Politics: PASS / FAIL
- Sports: PASS / FAIL
- Weather: PASS / FAIL
- Science: PASS / FAIL
- Tech: PASS / FAIL
- Personal advice: PASS / FAIL
- General knowledge: PASS / FAIL

### 📊 Token Usage
- Before: ___ tokens avg
- After: ___ tokens avg
- Reduction: ___%

### Notes:
[Any issues or observations]
```

---

## 🚀 Quick Test Commands

For developers:

```bash
# Start testing session
php artisan serve

# Monitor logs in real-time
tail -f storage/logs/laravel.log | grep -i "groq"

# Check database for chat history
php artisan tinker
>>> \App\Models\ChatMessage::latest()->take(5)->get(['role', 'message'])
```

---

**Last Updated:** 2025-10-10
**Version:** 1.0
**AI Model:** Groq Llama 3 (llama3-8b-8192)
