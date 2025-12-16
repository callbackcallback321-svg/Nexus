# 🚀 Nexus - Quick Start Guide

## Prerequisites

Before running, make sure you have:

1. **Python 3** installed (download from https://www.python.org/downloads/)
2. **PHP** installed (download from https://windows.php.net/download/ or use XAMPP/WAMP)

## Step-by-Step Instructions

### Step 1: Install Python Dependencies

Open PowerShell or Command Prompt in the project directory and run:

```powershell
python -m pip install -r requirements.txt
```

This installs: `requests`, `colorama`, `psutil`

### Step 2: Verify PHP Installation

Check if PHP is installed:

```powershell
php -v
```

**If PHP is not found:**
- Install PHP from https://windows.php.net/download/
- Or use XAMPP/WAMP (PHP is included)
- Add PHP to your system PATH

### Step 3: Run the Project

In the project directory, run:

```powershell
python st.py
```

**What happens:**
- ✅ Checks for dependencies (Python & PHP)
- ✅ Starts PHP web server on port **2525**
- ✅ Displays the web panel URL: `http://localhost:2525`

### Step 4: Access the Dashboard

1. Open your web browser
2. Go to: **http://localhost:2525**
3. Login with:
   - **Username**: `admin`
   - **Password**: `admin`

### Step 5: (Optional) Expose with Ngrok

For HTTPS access (required for camera/microphone/location features):

1. Download Ngrok: https://ngrok.com/download
2. In a **new** terminal, run:
   ```powershell
   ngrok http 2525
   ```
3. Copy the HTTPS URL (e.g., `https://xxxx.ngrok.io`)
4. Use this URL to access from other devices

## Using the Dashboard

### Main Features:

1. **View Templates**: All available templates are shown in a grid with icons
2. **Get Template Links**: Click "Get Link" on any template card to copy its URL
3. **Manage News**: Click the "News Management" card to add/edit breaking news
4. **Monitor Activity**: View logs in the collapsible logs section
5. **Control Listener**: Start/stop the listener, download logs, clear logs

### News Management:

1. Click the **"News Management"** card on the dashboard
2. Fill in the form:
   - News Title
   - Description
   - Upload Image (optional)
   - Set Timestamp and Priority
3. Click **"Add News"** to save
4. View all news items below the form
5. Delete news items using the delete button

## Stopping the Server

To stop the server:
- Press `Enter` in the terminal where `st.py` is running
- Or press `Ctrl+C`

## Troubleshooting

### ❌ "PHP not found" error
**Solution:** Install PHP and add it to PATH, or use XAMPP/WAMP

### ❌ "Module not found" error
**Solution:** Run `python -m pip install -r requirements.txt`

### ❌ Port 2525 already in use
**Solution:** 
- Find the process: `netstat -ano | findstr :2525`
- Kill the process or change port in `st.py` (line 9)

### ❌ Can't access from other devices
**Solution:** Use Ngrok to create a public HTTPS URL

### ❌ Camera/Microphone not working
**Solution:** These features require HTTPS. Use Ngrok or deploy to a server with SSL.

## Default Credentials

- **Username**: `admin`
- **Password**: `admin`

To change credentials, edit `nexus-web/config.php`

## Quick Command Summary

```powershell
# 1. Install dependencies
python -m pip install -r requirements.txt

# 2. Run the server
python st.py

# 3. (Optional) In another terminal, start Ngrok
ngrok http 2525
```

## Important Notes

⚠️ **HTTPS Required**: Camera, microphone, and location features require HTTPS. Use Ngrok for local development.

⚠️ **Security**: This tool is for educational and authorized testing purposes only.

---

**Need Help?** Check `WINDOWS_SETUP_GUIDE.md` for detailed troubleshooting.

