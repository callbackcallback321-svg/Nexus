<?php
/**
 * GeoIP Database Setup Script
 * Downloads and sets up the MaxMind GeoLite2 database
 */

echo "=== GeoIP Database Setup ===\n\n";

$mmdbPath = __DIR__ . '/geoip.mmdb';
$mmdbDir = __DIR__;

// Check if file already exists
if (file_exists($mmdbPath)) {
    echo "✓ geoip.mmdb already exists at: {$mmdbPath}\n";
    echo "  File size: " . number_format(filesize($mmdbPath) / 1024 / 1024, 2) . " MB\n";
    echo "  Last modified: " . date('Y-m-d H:i:s', filemtime($mmdbPath)) . "\n\n";
    
    $response = readline("Do you want to download a fresh copy? (y/n): ");
    if (strtolower(trim($response)) !== 'y') {
        echo "Setup cancelled.\n";
        exit(0);
    }
}

echo "Downloading GeoLite2-City database...\n";
echo "Note: This is a large file (~50-60 MB). Please be patient.\n\n";

// MaxMind GeoLite2 download URL (requires license key for direct download)
// Alternative: Use a mirror or manual download
$downloadUrl = "https://download.maxmind.com/app/geoip_download?edition_id=GeoLite2-City&license_key=YOUR_LICENSE_KEY&suffix=tar.gz";

echo "IMPORTANT: MaxMind requires a free license key for GeoLite2 downloads.\n";
echo "Please follow these steps:\n\n";
echo "1. Visit: https://www.maxmind.com/en/accounts/current/license-key\n";
echo "2. Sign up for a free MaxMind account (if you don't have one)\n";
echo "3. Generate a license key\n";
echo "4. Download GeoLite2-City.mmdb from:\n";
echo "   https://www.maxmind.com/en/accounts/current/geoip/downloads\n";
echo "5. Place the file as: {$mmdbPath}\n\n";

// Alternative: Try to download from a public mirror (if available)
echo "Alternatively, you can use this script to download from a public source:\n";
echo "Would you like to try downloading from a public mirror? (y/n): ";
$response = readline();

if (strtolower(trim($response)) === 'y') {
    // Try downloading from GitHub releases or other public sources
    $mirrorUrls = array(
        // GitHub mirror (if available)
        'https://github.com/P3TERX/GeoLite.mmdb/raw/download/GeoLite2-City.mmdb',
        // Alternative sources
        'https://cdn.jsdelivr.net/gh/P3TERX/GeoLite.mmdb@download/GeoLite2-City.mmdb'
    );
    
    $downloaded = false;
    foreach ($mirrorUrls as $url) {
        echo "Trying: {$url}\n";
        
        $ch = curl_init($url);
        $fp = fopen($mmdbPath, 'wb');
        curl_setopt($ch, CURLOPT_FILE, $fp);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300); // 5 minute timeout
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
        
        $result = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        fclose($fp);
        
        if ($result && $httpCode == 200 && file_exists($mmdbPath) && filesize($mmdbPath) > 1000000) {
            echo "✓ Successfully downloaded geoip.mmdb!\n";
            echo "  File size: " . number_format(filesize($mmdbPath) / 1024 / 1024, 2) . " MB\n";
            $downloaded = true;
            break;
        } else {
            if (file_exists($mmdbPath)) {
                unlink($mmdbPath);
            }
            echo "✗ Download failed from this source.\n";
        }
    }
    
    if (!$downloaded) {
        echo "\n✗ Automatic download failed.\n";
        echo "Please download manually:\n";
        echo "1. Visit: https://www.maxmind.com/en/accounts/current/geoip/downloads\n";
        echo "2. Download GeoLite2-City.mmdb\n";
        echo "3. Place it at: {$mmdbPath}\n";
        exit(1);
    }
} else {
    echo "\nManual download instructions:\n";
    echo "1. Visit: https://www.maxmind.com/en/accounts/current/geoip/downloads\n";
    echo "2. Download GeoLite2-City.mmdb\n";
    echo "3. Place it at: {$mmdbPath}\n";
    exit(0);
}

// Verify the file
if (file_exists($mmdbPath)) {
    $fileSize = filesize($mmdbPath);
    if ($fileSize > 1000000) { // At least 1 MB
        echo "\n✓ Setup complete!\n";
        echo "  File location: {$mmdbPath}\n";
        echo "  File size: " . number_format($fileSize / 1024 / 1024, 2) . " MB\n";
        echo "\nThe GeoIP database is now ready to use.\n";
    } else {
        echo "\n✗ Downloaded file seems too small. Please check the file.\n";
        exit(1);
    }
} else {
    echo "\n✗ File not found. Setup failed.\n";
    exit(1);
}

?>

