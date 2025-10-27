# Kitchen Notification Audio Setup

## Required File
**Filename:** `kitchen-bell.mp3`
**Location:** `public/sounds/kitchen-bell.mp3`

## How to Add the Audio File

### Option 1: Download Free Sound Effect
1. Visit: https://pixabay.com/sound-effects/search/bell/
2. Search for "kitchen bell" or "notification bell"
3. Download as MP3
4. Rename to `kitchen-bell.mp3`
5. Place in `public/sounds/` folder

### Option 2: Use YouTube Audio Library
1. Visit: https://www.youtube.com/audiolibrary
2. Search for "bell" or "notification"
3. Download and convert to MP3
4. Rename to `kitchen-bell.mp3`
5. Place in `public/sounds/` folder

### Option 3: Create Simple Beep with Audacity (Free)
1. Download Audacity (free audio editor)
2. Generate → Tone → Choose frequency 800Hz, 0.5 seconds
3. Export as MP3
4. Save as `kitchen-bell.mp3` in `public/sounds/`

## Recommended Specifications
- **Format:** MP3
- **Duration:** 0.5 - 2 seconds (short and punchy)
- **Volume:** Moderate (not too loud)
- **Type:** Bell, chime, or notification sound

## Testing
Once added, test by:
1. Go to Kitchen Dashboard
2. Manually set a station load to 85%+
3. Should hear notification sound
4. Check browser console for errors

## Alternative: Disable Audio
If you don't want audio notifications, simply remove or comment out line 87-89 in:
`public/js/admin/kitchen-dashboard.js`

```javascript
// playNotificationSound() {
//     const audio = new Audio('/sounds/kitchen-bell.mp3');
//     audio.volume = 0.5;
//     audio.play().catch(e => console.log('Audio play failed:', e));
// }
```

## Current Status
⚠️ Audio file not included. Add `kitchen-bell.mp3` to enable sound notifications.
