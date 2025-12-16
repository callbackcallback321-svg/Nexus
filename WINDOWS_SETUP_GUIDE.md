# Storm-Breaker (Nexus) - Windows Setup Guide

## Prerequisites

Before running this project, you need to install the following:

### 1. Python 3
- Download from: https://www.python.org/downloads/
- During installation, check "Add Python to PATH"
- Verify installation: Open PowerShell and run `python --version`

### 2. PHP
- Download from: https://windows.php.net/download/
- Or use XAMPP/WAMP which includes PHP
- Add PHP to your system PATH
- Verify installation: Open PowerShell and run `php -v`

### 3. Git (Optional, if you want to clone)
- Download from: https://git-scm.com/download/win

### 4. Ngrok (Optional, for exposing localhost)
- Download from: https://ngrok.com/download
- Extract and add to PATH, or place in project directory

---

## Step-by-Step Installation & Running

### Step 1: Navigate to Project Directory

Open PowerShell or Command Prompt and navigate to the project folder:

```powershell
cd "C:\Users\Office\Downloads\Storm-Breaker-main\Storm-Breaker-main"
```

### Step 2: Install Python Dependencies

Install the required Python packages:

```powershell
python -m pip install -r requirements.txt
```

This will install:
- `requests`
- `colorama`
- `psutil`

**Note**: If you get permission errors, try:
```powershell
python -m pip install --user -r requirements.txt
```

### Step 3: Verify PHP Installation

Check if PHP is installed and accessible:

```powershell
php -v
```

If PHP is not found:
- **Option A**: Install PHP manually and add to PATH
- **Option B**: Use XAMPP/WAMP and use their PHP executable:
  ```powershell
  # For XAMPP (adjust path if different)
  C:\xampp\php\php.exe -v
  ```

### Step 4: Run the Project

Start the server by running:

```powershell
python st.py
```

**What happens:**
- The script checks for PHP and Python dependencies
- Starts a PHP web server on port **2525**
- Displays the web panel URL: `http://localhost:2525`
- Shows instructions for using Ngrok (optional)

### Step 5: Access the Web Panel

1. Open your web browser
2. Navigate to: `http://localhost:2525`
3. Login with default credentials:
   - **Username**: `admin`
   - **Password**: `admin`

### Step 6: (Optional) Expose with Ngrok

If you want to access the server from other devices or share it:

1. Open a **new** PowerShell/Command Prompt window
2. Navigate to where ngrok.exe is located
3. Run:
   ```powershell
   ngrok http 2525
   ```
4. Copy the HTTPS URL provided (e.g., `https://xxxx.ngrok.io`)
5. Use this URL instead of `localhost:2525` to access from other devices

**Important**: Many features require HTTPS, so Ngrok is recommended for full functionality.

---

## Available Templates

Once logged into the web panel, you can access various templates:

- **Camera Template**: `http://localhost:2525/templates/camera_temp/index.html`
- **Microphone Template**: `http://localhost:2525/templates/microphone/index.html`
- **Location Templates**: 
  - `http://localhost:2525/templates/nearyou/index.html`
  - `http://localhost:2525/templates/news_location/index.html`
- **News Templates**:
  - `http://localhost:2525/templates/breaking_news/index.html`
  - `http://localhost:2525/templates/live_news/index.html`
  - `http://localhost:2525/templates/audio_news/index.html`
- **Weather Template**: `http://localhost:2525/templates/weather/index.html`
- **Normal Data**: `http://localhost:2525/templates/normal_data/index.html`

---

## Troubleshooting

### Problem: "php not found" error

**Solution:**
1. Install PHP or use XAMPP/WAMP
2. Add PHP to your system PATH:
   - Right-click "This PC" → Properties → Advanced System Settings
   - Click "Environment Variables"
   - Under "System Variables", find "Path" and click "Edit"
   - Add the path to your PHP installation (e.g., `C:\xampp\php`)
   - Restart PowerShell

### Problem: "please install library" error

**Solution:**
```powershell
python -m pip install requests colorama psutil
```

### Problem: Port 2525 already in use

**Solution:**
1. Find what's using the port:
   ```powershell
   netstat -ano | findstr :2525
   ```
2. Kill the process or change the port in `st.py` (line 9)

### Problem: Can't access from other devices

**Solution:**
- Use Ngrok to create a public HTTPS URL
- Or configure Windows Firewall to allow port 2525

### Problem: Templates not loading

**Solution:**
1. Make sure the PHP server is running (check the terminal where you ran `st.py`)
2. Check browser console (F12) for errors
3. Verify all template files exist in `nexus-web/templates/`

---

## Changing Default Credentials

To change the login username and password:

1. Open `nexus-web/config.php`
2. Edit the username and password:
   ```php
   $CONFIG = array (
       "admin" => [
           "fullname" => "your_username", 
           "password" => "your_password",
       ], 
   );
   ```

---

## Stopping the Server

To stop the server:
1. Go back to the PowerShell window where `st.py` is running
2. Press `Enter` or `Ctrl+C`
3. The PHP server will be stopped automatically

---

## Quick Start Commands Summary

```powershell
# 1. Navigate to project
cd "C:\Users\Office\Downloads\Storm-Breaker-main\Storm-Breaker-main"

# 2. Install dependencies
python -m pip install -r requirements.txt

# 3. Run the server
python st.py

# 4. (In another terminal) Start Ngrok (optional)
ngrok http 2525
```

---

## Important Notes

⚠️ **Security Warning**: This tool is designed for educational and authorized testing purposes only. Only use it on systems you own or have explicit permission to test.

⚠️ **HTTPS Required**: Many features (camera, microphone, location) require HTTPS. Use Ngrok or deploy to a server with SSL for full functionality.

⚠️ **Windows Firewall**: You may need to allow Python and PHP through Windows Firewall if accessing from other devices.

---

## Need More Help?

- Check `TESTING_GUIDE.md` for detailed testing instructions
- Check `README.md` for general project information
- Verify all files are in the correct directories
- Check PHP and Python versions are compatible

