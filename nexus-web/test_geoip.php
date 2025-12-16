<?php
/**
 * Quick test to verify GeoIP database is working
 */

$mmdbPath = __DIR__ . '/geoip.mmdb';

echo "=== GeoIP Database Test ===\n\n";

if (file_exists($mmdbPath)) {
    echo "✓ Database file found!\n";
    echo "  Location: {$mmdbPath}\n";
    echo "  Size: " . number_format(filesize($mmdbPath) / 1024 / 1024, 2) . " MB\n";
    echo "  Last modified: " . date('Y-m-d H:i:s', filemtime($mmdbPath)) . "\n\n";
    
    // Check if MaxMind library is available
    if (class_exists('GeoIp2\Database\Reader')) {
        echo "✓ MaxMind GeoIP2 PHP library is installed!\n\n";
        
        try {
            $reader = new GeoIp2\Database\Reader($mmdbPath);
            echo "Testing with a sample IP (8.8.8.8 - Google DNS)...\n";
            $record = $reader->city('8.8.8.8');
            
            echo "\n✓ Database is working correctly!\n\n";
            echo "Sample lookup results:\n";
            echo "  Country: " . ($record->country->name ?? 'N/A') . "\n";
            echo "  City: " . ($record->city->name ?? 'N/A') . "\n";
            echo "  Latitude: " . ($record->location->latitude ?? 'N/A') . "\n";
            echo "  Longitude: " . ($record->location->longitude ?? 'N/A') . "\n";
            echo "  Timezone: " . ($record->location->timeZone ?? 'N/A') . "\n";
            
        } catch (Exception $e) {
            echo "✗ Error reading database: " . $e->getMessage() . "\n";
            echo "  The file might be corrupted. Please re-download it.\n";
        }
    } else {
        echo "⚠ MaxMind GeoIP2 PHP library is NOT installed.\n";
        echo "  The system will use API fallback instead.\n";
        echo "  To use the database directly, install: composer require maxmind/geoip2\n";
        echo "  Or download from: https://github.com/maxmind/GeoIP2-php\n\n";
        echo "  The system will still work with API fallback, but using the\n";
        echo "  database directly is faster and more accurate.\n";
    }
    
} else {
    echo "✗ Database file NOT found!\n";
    echo "  Expected location: {$mmdbPath}\n";
    echo "  Please ensure the file is named 'geoip.mmdb' and placed in the nexus-web directory.\n";
}

echo "\n=== Test Complete ===\n";

?>

