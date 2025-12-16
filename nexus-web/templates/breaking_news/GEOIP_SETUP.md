# GeoIP Setup Guide

## Overview
The breaking news template now collects comprehensive visitor fingerprinting data including:
- Public IP address
- GeoIP information (country, city, coordinates, ISP, etc.)
- Browser details (user agent, version, platform)
- OS information
- Canvas fingerprint
- WebGL fingerprint
- Screen resolution and display properties
- Hardware information (CPU cores, memory)
- Plugins and MIME types
- Storage capabilities
- Network information
- Timezone and locale

## GeoIP Database Setup

### Quick Setup (Automated)

**For Linux/Mac:**
```bash
cd nexus-web
chmod +x download_geoip.sh
./download_geoip.sh
```

**For Windows:**
```cmd
cd nexus-web
download_geoip.bat
```

**For PHP (any platform):**
```bash
cd nexus-web
php setup_geoip.php
```

### Option 1: Using MaxMind GeoIP2 Database (Recommended)

#### Method A: Automated Download Script

1. **Run the setup script:**
   - Linux/Mac: `./download_geoip.sh`
   - Windows: `download_geoip.bat`
   - PHP: `php setup_geoip.php`

2. **The script will:**
   - Attempt to download from public mirrors
   - Place the file at `nexus-web/geoip.mmdb`
   - Verify the download

#### Method B: Manual Download

1. **Get MaxMind License Key (Free):**
   - Visit: https://www.maxmind.com/en/accounts/current/license-key
   - Sign up for a free MaxMind account
   - Generate a license key

2. **Download GeoIP2 Database:**
   - Visit: https://www.maxmind.com/en/accounts/current/geoip/downloads
   - Download `GeoLite2-City.mmdb`
   - Place it in: `nexus-web/geoip.mmdb`

3. **Install MaxMind PHP Library (Optional but Recommended):**
   ```bash
   composer require maxmind/geoip2
   ```
   Or download manually from: https://github.com/maxmind/GeoIP2-php

4. **Benefits:**
   - More accurate GeoIP data
   - Includes accuracy radius
   - Supports subdivision_2 (county level)
   - No API rate limits
   - Faster lookups (local database)

### Option 2: API Fallback (Default - No Setup Required)

- If `geoip.mmdb` is not found, the system automatically uses ip-api.com
- Works immediately without any setup
- Free tier: 45 requests/minute
- Good for testing, but MaxMind database is recommended for production

## How It Works

1. **On Page Load:**
   - JavaScript collects all fingerprinting data
   - Sends data to `handler.php` via AJAX

2. **Server-Side Processing:**
   - `handler.php` extracts the visitor's IP address
   - Looks up GeoIP information using:
     - MaxMind mmdb file (if available)
     - ip-api.com API (fallback)
   - Combines all data and logs to `result.txt`

3. **Log File:**
   - All visitor data is saved to `result.txt`
   - Each entry includes complete fingerprinting information
   - Formatted for easy reading and analysis

## Log File Location
`nexus-web/templates/breaking_news/result.txt`

## Data Collected

### IP & GeoIP
- Public IP address
- Country, Region, City
- Postal code
- Latitude/Longitude
- Timezone
- ISP and Organization
- ASN

### Browser Fingerprint
- User Agent
- Browser name and version
- Platform
- Canvas fingerprint
- WebGL fingerprint
- Plugins list
- MIME types

### Device Information
- OS name and version
- CPU architecture
- Screen resolution
- Color depth
- Hardware concurrency
- Device memory

### Privacy & Network
- Do Not Track status
- Cookies enabled
- Storage capabilities
- Network type and speed
- Connection RTT

## Notes

- All data collection happens automatically on page load
- No user interaction required
- Data is logged server-side for security
- IP address is detected from server headers (handles proxies correctly)
- GeoIP lookup is performed server-side to avoid exposing API keys

