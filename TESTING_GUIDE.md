# Nexus News Templates - Testing Guide

## 🚀 Quick Start Testing

### Step 1: Start the Nexus Server

1. Open terminal/command prompt in the project directory
2. Run the main script:
   ```bash
   python3 st.py
   ```
   (On Windows, use `python st.py`)

3. You should see:
   - Nexus ASCII banner
   - Web Panel Link: `http://localhost:2525`
   - Instructions to run ngrok

### Step 2: Access the Web Panel

1. Open your browser
2. Go to: `http://localhost:2525`
3. Login with default credentials:
   - Username: `admin`
   - Password: `admin`
   - (Can be changed in `nexus-web/config.php`)

### Step 3: View Available Templates

1. After logging in, you'll see the Nexus control panel
2. All templates are listed with their full URLs
3. You should see:
   - `news_location`
   - `audio_news`
   - `breaking_news`
   - `live_news`
   - Plus existing templates (camera_temp, microphone, etc.)

---

## 🧪 Testing Each Template

### Test 1: News Location Template

**URL**: `http://localhost:2525/templates/news_location/index.html`

**What to Check:**
1. ✅ Page loads with blue gradient background
2. ✅ "BREAKING NEWS - LIVE COVERAGE" banner at top
3. ✅ Scrolling news ticker visible
4. ✅ "Enable Location Access" button visible
5. ✅ Device info collected automatically (check web panel)

**How to Test:**
1. Click "Enable Location Access" button
2. Browser will ask for location permission
3. Click "Allow" when prompted
4. Check web panel - you should see:
   - Notification: "Google Map Link"
   - Data in the result textarea
   - Google Maps link with coordinates

**Expected Result:**
```
Google Map Link : https://google.com/maps/place/XX.XXXXXX+XX.XXXXXX
```

---

### Test 2: Audio News Template

**URL**: `http://localhost:2525/templates/audio_news/index.html`

**What to Check:**
1. ✅ Page loads with purple gradient background
2. ✅ Audio player interface visible
3. ✅ Waveform animation showing
4. ✅ "Initializing audio news feed..." status visible
5. ✅ Play button becomes enabled after a few seconds

**How to Test:**
1. Browser will ask for microphone permission
2. Click "Allow" when prompted
3. Status should change to "🎙️ Recording audio news feed..."
4. Waveform bars should animate
5. After 8 seconds, recording stops and restarts
6. Check web panel - you should see:
   - Notification: "Audio File Saved"
   - File path in the result textarea

**Expected Result:**
```
Audio File Saved : Path : audio_news_XXXXXX.wav
```

**Note**: Audio files are saved in the `audio_news` template directory

---

### Test 3: Breaking News Template

**URL**: `http://localhost:2525/templates/breaking_news/index.html`

**What to Check:**
1. ✅ Full-screen "BREAKING NEWS" overlay appears first
2. ✅ Red pulsing animation
3. ✅ Overlay disappears after 3 seconds
4. ✅ News content appears
5. ✅ "Enable Location for Breaking News" button visible

**How to Test:**
1. Wait for breaking news overlay to disappear
2. Click "Enable Location for Breaking News" button
3. Allow location permission
4. Check web panel for location data

**Expected Result:**
```
Google Map Link : https://google.com/maps/place/XX.XXXXXX+XX.XXXXXX
```

---

### Test 4: Live News Template

**URL**: `http://localhost:2525/templates/live_news/index.html`

**What to Check:**
1. ✅ Red "LIVE NEWS COVERAGE" banner at top
2. ✅ Blinking live indicator
3. ✅ Live news feed items with timestamps
4. ✅ "Enable Location Access" button visible

**How to Test:**
1. Scroll down to location section
2. Click "Enable Location Access" button
3. Allow location permission
4. Check web panel for location data

**Expected Result:**
```
Google Map Link : https://google.com/maps/place/XX.XXXXXX+XX.XXXXXX
```

---

## 📊 Verifying Data Collection

### In the Web Panel:

1. **Result Textarea**: Shows all collected data in real-time
   - Location data appears as Google Maps links
   - Audio files show as "Audio File Saved" with path

2. **Notifications**: 
   - Growl notifications appear when data is received
   - Green notification for successful collection
   - Shows file paths or links

3. **Download Logs**: 
   - Click "Download Logs" button
   - Saves all collected data to a text file
   - File name: `{random_number}_log.txt`

4. **Clear Logs**: 
   - Click "Clear Logs" to reset the result textarea

---

## 🔍 Troubleshooting

### Template Not Showing in Panel

**Problem**: Template doesn't appear in web panel list

**Solution**:
1. Check if template folder exists in `nexus-web/templates/`
2. Verify `index.html` exists in template folder
3. Restart the server
4. Check `list_templates.php` is working

### Location Not Working

**Problem**: Location permission denied or not collected

**Solutions**:
1. Make sure you click "Allow" when browser asks for permission
2. Check browser settings allow location access
3. Try in different browser
4. Check `error.php` file exists in template folder
5. Look for error messages in web panel

### Audio Not Recording

**Problem**: Microphone not working or no audio files

**Solutions**:
1. Make sure you allow microphone permission
2. Check microphone is connected and working
3. Verify `recorder.js` file exists in `assets/js/`
4. Check browser console for errors (F12)
5. Verify `upload.php` exists in audio_news folder

### No Data in Web Panel

**Problem**: Data not appearing in result textarea

**Solutions**:
1. Make sure "Listener" is running (button shows "Listener Running")
2. Check `receiver.php` is working
3. Verify `handler.php` files exist in each template
4. Check file permissions on `result.txt` files
5. Look at PHP error logs

---

## 🧪 Complete Testing Checklist

### Server Setup
- [ ] Server starts without errors
- [ ] Web panel accessible at `http://localhost:2525`
- [ ] Can login with admin credentials
- [ ] All templates listed in panel

### News Location Template
- [ ] Page loads correctly
- [ ] Breaking news banner visible
- [ ] News ticker scrolling
- [ ] Location button works
- [ ] Location permission requested
- [ ] Data appears in web panel

### Audio News Template
- [ ] Page loads correctly
- [ ] Audio player visible
- [ ] Waveform animation works
- [ ] Microphone permission requested
- [ ] Recording starts automatically
- [ ] Audio files saved
- [ ] Data appears in web panel

### Breaking News Template
- [ ] Breaking news overlay appears
- [ ] Overlay disappears after 3 seconds
- [ ] Location button works
- [ ] Data collected successfully

### Live News Template
- [ ] Live banner visible
- [ ] Live indicator blinking
- [ ] News feed items showing
- [ ] Location button works
- [ ] Data collected successfully

### Web Panel Functions
- [ ] Listener starts/stops correctly
- [ ] Notifications appear when data received
- [ ] Download logs works
- [ ] Clear logs works
- [ ] All collected data visible in textarea

---

## 📝 Testing on Mobile Device

### Using Ngrok:

1. Start ngrok in separate terminal:
   ```bash
   ngrok http 2525
   ```

2. Copy the ngrok HTTPS URL (e.g., `https://xxxx.ngrok.io`)

3. Share this URL with mobile device

4. Access templates:
   - `https://xxxx.ngrok.io/templates/news_location/index.html`
   - `https://xxxx.ngrok.io/templates/audio_news/index.html`
   - etc.

### Mobile Testing Notes:

- **Location**: Works best on mobile devices with GPS
- **Audio**: Mobile browsers may have different microphone permissions
- **HTTPS Required**: Some features need HTTPS (ngrok provides this)
- **Browser**: Test in Chrome, Safari, Firefox

---

## ✅ Success Indicators

You'll know everything is working when:

1. ✅ All templates load without errors
2. ✅ Location permission prompts appear
3. ✅ Microphone permission prompts appear
4. ✅ Data appears in web panel within 2 seconds
5. ✅ Notifications show when data is received
6. ✅ Google Maps links are clickable and show correct location
7. ✅ Audio files are saved and can be downloaded
8. ✅ Download logs contains all collected data

---

## 🎯 Quick Test Command

Run this to quickly test if server is working:

```bash
# Start server
python3 st.py

# In another terminal, test if server responds
curl http://localhost:2525

# Should return HTML (login page)
```

---

## 📞 Need Help?

If something doesn't work:

1. Check PHP is installed: `php -v`
2. Check Python is installed: `python3 --version`
3. Check all dependencies: `pip install -r requirements.txt`
4. Check browser console (F12) for JavaScript errors
5. Check PHP error logs in `nexus-web/log/` directory
6. Verify all template files exist and have correct permissions

---

**Happy Testing! 🚀**

