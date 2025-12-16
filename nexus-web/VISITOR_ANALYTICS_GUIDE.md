# Visitor Analytics Dashboard - User Guide

## Overview

The Visitor Analytics Dashboard provides a comprehensive interface to view, analyze, and export all visitor information collected from the breaking news template.

## Access

**URL:** `visitor_analytics.php`

**Access Control:** Requires admin login (same authentication as main panel)

**Quick Access:** Click "Visitor Analytics" button in the main dashboard

---

## Features

### 📊 Statistics Dashboard

**Real-time Statistics Cards:**
- **Total Visitors:** Total number of visitor records
- **Unique IPs:** Number of unique IP addresses
- **Countries:** Number of different countries
- **Filtered Results:** Current filtered count

### 🔍 Filtering & Search

**Available Filters:**
- **Search:** Full-text search across all visitor data
- **Country:** Filter by country name
- **IP Address:** Filter by specific IP address
- **Browser:** Filter by browser type

**How to Use:**
1. Enter filter criteria in the filter section
2. Click "Apply Filters"
3. Results update automatically
4. Click "Clear Filters" to reset

### 📈 Analytics

**Top Countries:**
- Shows top 10 countries by visitor count
- Displays visit count for each country

**Top Browsers:**
- Shows top 10 browsers by usage
- Displays usage count for each browser

### 📋 Visitor Records Table

**Columns:**
- **Timestamp:** When the visitor accessed the template
- **IP Address:** Visitor's public IP address
- **Location:** City and country with map link
- **Browser:** Browser name and version
- **OS:** Operating system
- **ISP:** Internet Service Provider
- **Actions:** View full details button

**Features:**
- Hover effect on rows
- Click "Details" to see complete visitor information
- Map link opens Google Maps with coordinates
- Pagination (25 records per page)

### 👁️ Detailed View

**Full Visitor Information Includes:**
- Complete IP and GeoIP information
- Location data (latitude, longitude, accuracy radius)
- Geographic hierarchy (continent, country, subdivisions)
- Browser information
- Operating system details
- Screen and display properties
- Hardware information
- Canvas and WebGL fingerprints
- Plugins and MIME types
- Storage capabilities
- Network information
- Timezone and locale
- Page URL and referrer

### 💾 Export Functionality

**Export Options:**
- Click "Export Data" button
- Downloads complete log file as text
- Filename: `visitor_logs_YYYY-MM-DD.txt`

### 🔄 Auto-Refresh

- Page automatically refreshes every 30 seconds
- Only refreshes when tab is visible
- Manual refresh button available

---

## Data Collection

### When Data is Collected

Visitor data is automatically collected when:
1. User visits the breaking news template
2. JavaScript fingerprinting runs on page load
3. Data is sent to `handler.php`
4. GeoIP lookup is performed
5. All data is logged to `result.txt`

### What Data is Collected

**IP & GeoIP:**
- Public IP address
- Country, Region, City
- Postal code
- Latitude/Longitude
- Accuracy radius (if MaxMind library installed)
- Timezone
- ISP and Organization
- ASN
- Continent information
- Subdivision 1 (State/Province)
- Subdivision 2 (County)
- EU membership status

**Browser Fingerprint:**
- User Agent
- Browser name and version
- Platform
- Canvas fingerprint
- WebGL fingerprint
- Plugins list
- MIME types

**Device Information:**
- OS name and version
- CPU architecture
- Screen resolution
- Color depth
- Hardware concurrency
- Device memory

**Privacy & Network:**
- Do Not Track status
- Cookies enabled
- Storage capabilities
- Network type and speed
- Connection RTT

---

## Usage Tips

### Finding Specific Visitors

1. **By IP:** Use IP filter to find specific IP addresses
2. **By Country:** Filter by country to see all visitors from a region
3. **By Browser:** Filter by browser to see browser-specific analytics
4. **Search:** Use search for any text in the logs

### Analyzing Trends

1. Check "Top Countries" to see geographic distribution
2. Check "Top Browsers" to see browser usage
3. Review timestamps to see visit patterns
4. Export data for external analysis

### Viewing Full Details

1. Click "Details" button on any visitor row
2. Modal shows complete raw data
3. Scroll to see all information
4. Close modal when done

---

## File Locations

**Log File:** `nexus-web/templates/breaking_news/result.txt`

**Analytics Page:** `nexus-web/visitor_analytics.php`

**Handler:** `nexus-web/templates/breaking_news/handler.php`

---

## Security

- **Authentication Required:** Same login as main panel
- **Server-Side Processing:** All data processing is server-side
- **No Client Exposure:** Sensitive data never exposed to client

---

## Troubleshooting

### No Visitors Showing

- Check if `result.txt` file exists
- Verify breaking news template is being accessed
- Check browser console for JavaScript errors
- Verify `handler.php` is working

### Filters Not Working

- Clear all filters and try again
- Check for typos in filter values
- Verify data format in log file

### Export Not Working

- Check file permissions on `result.txt`
- Verify file exists and is readable
- Check server error logs

---

## Future Enhancements

Potential features to add:
- Date range filtering
- Advanced analytics charts
- CSV export
- Real-time updates via WebSocket
- Email alerts for specific events
- Visitor session tracking
- Geographic heat map
- Time-based analytics

---

## Support

For issues or questions:
1. Check log file format
2. Verify handler.php is processing correctly
3. Check browser console for errors
4. Review server error logs

