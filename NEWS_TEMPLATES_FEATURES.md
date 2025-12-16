# Nexus News Templates - Features & Functionalities

## Overview
This document explains all the new news-themed templates added to the Nexus project and their functionalities.

---

## 📍 **1. News Location Template** (`news_location`)

### Purpose
Collects GPS/location data from smartphones using a news channel theme.

### Features
- **Breaking News Banner**: Eye-catching red banner with "BREAKING NEWS - LIVE COVERAGE"
- **News Ticker**: Scrolling news ticker with live indicator
- **Professional News Design**: Blue gradient background with glassmorphism effects
- **Location Prompt**: Convincing explanation about why location is needed for personalized news
- **Real-time Updates**: Shows status when location is accessed

### Functionality
1. **Device Information Collection**: Automatically collects device info on page load using `mydata()` function
2. **Location Access**: When user clicks "Enable Location Access" button:
   - Requests GPS coordinates via `navigator.geolocation.getCurrentPosition()`
   - Captures latitude and longitude
   - Sends data to `handler.php` with Google Maps link format
   - Shows success message to user
3. **Error Handling**: Captures and logs location permission errors (denied, unavailable, timeout)

### Data Collected
- Device information (OS, browser, IP, CPU, resolution, timezone, language)
- GPS coordinates (latitude, longitude)
- Google Maps link with exact location

### Files
- `index.html` - Main template page
- `handler.php` - Processes and saves location data
- `error.php` - Handles location permission errors
- `result.txt` - Stores collected data

---

## 🎙️ **2. Audio News Template** (`audio_news`)

### Purpose
Captures microphone/audio input using an audio news channel theme.

### Features
- **Audio Player Interface**: Professional audio news player design
- **Waveform Visualization**: Animated waveform bars showing audio activity
- **Recording Status Indicator**: Visual feedback showing recording status
- **Purple Gradient Design**: Modern purple gradient background
- **Interactive Controls**: Play button that activates recording

### Functionality
1. **Automatic Recording Start**: Begins recording 500ms after page load
2. **Microphone Access**: 
   - Requests microphone permission via `getUserMedia()`
   - Creates audio context for recording
   - Uses Recorder.js library to capture audio
3. **Continuous Recording**:
   - Records for 8 seconds intervals
   - Automatically stops and uploads audio file
   - Restarts recording after upload
4. **Audio Upload**: 
   - Converts recorded audio to WAV format
   - Uploads to server via `upload.php`
   - Saves with timestamp filename

### Data Collected
- Audio recordings in WAV format
- Timestamp of each recording
- Recording duration and quality

### Files
- `index.html` - Main template page with audio player UI
- `js/_app.js` - Audio recording logic using Recorder.js
- `handler.php` - Processes audio data
- `upload.php` - Handles audio file uploads
- `result.txt` - Stores recording information

---

## 🚨 **3. Breaking News Template** (`breaking_news`)

### Purpose
Collects location data using an urgent breaking news theme.

### Features
- **Breaking News Overlay**: Full-screen breaking news alert on page load
- **Pulsing Animation**: Red "BREAKING NEWS" title with pulsing effect
- **Dark Theme**: Black background with red accents for urgency
- **Auto-Hide Overlay**: Breaking news overlay disappears after 3 seconds
- **News Items**: Multiple news sections explaining location need

### Functionality
1. **Initial Impact**: Shows dramatic breaking news overlay for 3 seconds
2. **Location Collection**: 
   - Same location collection mechanism as news_location template
   - Collects GPS coordinates
   - Sends to handler.php
3. **Device Info**: Collects device information on page load

### Data Collected
- Device information
- GPS coordinates
- Google Maps location link

### Files
- `index.html` - Breaking news themed page
- `handler.php` - Processes location data
- `result.txt` - Stores collected data

---

## 📺 **4. Live News Template** (`live_news`)

### Purpose
Collects location data using a live news broadcast theme.

### Features
- **Live Banner**: Sticky red banner with blinking live indicator
- **Live Feed Interface**: Simulated live news feed with timestamps
- **Dark Blue Gradient**: Professional news channel color scheme
- **Real-time Updates**: Shows "LIVE" indicators on news items
- **Location Section**: Dedicated section for location access

### Functionality
1. **Live Feed Display**: Shows multiple "live" news items with timestamps
2. **Location Collection**: 
   - Collects GPS coordinates when user enables location
   - Sends location data to handler
   - Shows confirmation message
3. **Device Info**: Automatically collects device information

### Data Collected
- Device information
- GPS coordinates
- Google Maps location link

### Files
- `index.html` - Live news themed page
- `handler.php` - Processes location data
- `result.txt` - Stores collected data

---

## 🔧 **Technical Implementation**

### Location Collection Mechanism
All location templates use:
- `location.js` - Handles geolocation API calls
- `navigator.geolocation.getCurrentPosition()` - Gets GPS coordinates
- Error handling for permission denials
- Google Maps link generation

### Audio Collection Mechanism
Audio news template uses:
- `recorder.js` - Audio recording library
- `getUserMedia()` API - Microphone access
- `AudioContext` - Audio processing
- WAV format export and upload

### Data Storage
- All templates save data to `result.txt` in their respective directories
- Data format: `Google Map Link : https://google.com/maps/place/{lat}+{lon}`
- Audio files saved with timestamps

### Template Detection
- Templates are automatically detected by `list_templates.php`
- Scans `./templates/` directory
- Returns JSON array of available templates
- Web panel displays all templates automatically

---

## 📊 **Data Flow**

### Location Templates Flow:
1. User visits template URL
2. Page loads → `mydata()` collects device info
3. User clicks location button → `locate()` function called
4. Browser requests location permission
5. If granted → GPS coordinates captured
6. Data sent to `handler.php` via POST
7. Saved to `result.txt`
8. Web panel listener retrieves data every 2 seconds

### Audio Template Flow:
1. User visits audio news template
2. Page loads → Recording starts automatically (500ms delay)
3. Microphone permission requested
4. If granted → Audio recording begins
5. Records for 8 seconds
6. Audio converted to WAV blob
7. Uploaded to `upload.php` via FormData
8. File saved on server
9. Recording restarts automatically

---

## 🎯 **Use Cases**

### News Location Template:
- Collect location for "personalized news delivery"
- Convincing news channel interface
- Multiple news items explaining location need

### Audio News Template:
- Collect audio for "voice-activated news"
- Professional audio player interface
- Continuous recording capability

### Breaking News Template:
- Urgent breaking news theme
- Creates sense of urgency
- Full-screen impact on load

### Live News Template:
- Live broadcast simulation
- Real-time news feed appearance
- Professional news channel look

---

## 🔐 **Security & Privacy Notes**

⚠️ **Important**: These templates are designed for security testing and educational purposes. They:
- Request sensitive permissions (location, microphone)
- Collect personal data (GPS coordinates, audio recordings)
- Should only be used with proper authorization
- Require user consent (browser permission prompts)

---

## 📝 **Summary**

**Total New Templates**: 4

1. **news_location** - GPS collection via news channel theme
2. **audio_news** - Microphone access via audio news theme  
3. **breaking_news** - GPS collection via breaking news theme
4. **live_news** - GPS collection via live news theme

**Features Added**:
- ✅ News-themed location collection (3 templates)
- ✅ Audio news microphone collection (1 template)
- ✅ Professional news channel designs
- ✅ Automatic template detection
- ✅ Error handling for permissions
- ✅ Real-time data collection
- ✅ Continuous audio recording

All templates are automatically detected by the Nexus web panel and will appear in the template list when the server is running.

