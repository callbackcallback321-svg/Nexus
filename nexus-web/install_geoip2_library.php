<?php
/**
 * MaxMind GeoIP2 Library Installer
 * Alternative to composer - Downloads and sets up the library manually
 */

echo "=== MaxMind GeoIP2 Library Installer ===\n\n";

$vendorDir = __DIR__ . '/vendor';
$maxmindDir = $vendorDir . '/maxmind';
$geoip2Dir = $maxmindDir . '/geoip2';
$readerDir = $maxmindDir . '/maxmind-db/reader';

// Create directories
if (!is_dir($vendorDir)) {
    mkdir($vendorDir, 0755, true);
    echo "✓ Created vendor directory\n";
}

if (!is_dir($maxmindDir)) {
    mkdir($maxmindDir, 0755, true);
    echo "✓ Created maxmind directory\n";
}

// Function to download and extract ZIP
function downloadAndExtract($url, $destination, $name) {
    echo "Downloading {$name}...\n";
    
    $zipFile = sys_get_temp_dir() . '/' . basename($url);
    
    // Download
    $ch = curl_init($url);
    $fp = fopen($zipFile, 'wb');
    curl_setopt($ch, CURLOPT_FILE, $fp);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0');
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    fclose($fp);
    
    if (!$result || $httpCode != 200) {
        echo "✗ Failed to download {$name}\n";
        return false;
    }
    
    // Extract
    $zip = new ZipArchive;
    if ($zip->open($zipFile) === TRUE) {
        $zip->extractTo($destination);
        $zip->close();
        unlink($zipFile);
        echo "✓ Extracted {$name}\n";
        return true;
    } else {
        echo "✗ Failed to extract {$name}\n";
        unlink($zipFile);
        return false;
    }
}

// Download GeoIP2 library
$geoip2Url = 'https://github.com/maxmind/GeoIP2-php/archive/refs/tags/v2.13.0.zip';
if (downloadAndExtract($geoip2Url, $maxmindDir, 'GeoIP2 library')) {
    // Rename extracted folder
    $extracted = $maxmindDir . '/GeoIP2-php-2.13.0';
    if (is_dir($extracted)) {
        if (is_dir($geoip2Dir)) {
            rmdir_recursive($geoip2Dir);
        }
        rename($extracted, $geoip2Dir);
    }
}

// Download MaxMind DB Reader
$readerUrl = 'https://github.com/maxmind/MaxMind-DB-Reader-php/archive/refs/tags/v1.11.1.zip';
if (downloadAndExtract($readerUrl, $maxmindDir, 'MaxMind DB Reader')) {
    // Rename extracted folder
    $extracted = $maxmindDir . '/MaxMind-DB-Reader-php-1.11.1';
    if (is_dir($extracted)) {
        $readerParent = dirname($readerDir);
        if (!is_dir($readerParent)) {
            mkdir($readerParent, 0755, true);
        }
        if (is_dir($readerDir)) {
            rmdir_recursive($readerDir);
        }
        rename($extracted, $readerDir);
    }
}

// Create autoloader
$autoloaderPath = $vendorDir . '/autoload.php';
$autoloader = <<<'PHP'
<?php
// Autoloader for MaxMind GeoIP2 libraries
spl_autoload_register(function ($class) {
    $baseDir = __DIR__ . '/maxmind/';
    
    // GeoIP2 namespace
    if (strpos($class, 'GeoIp2\\') === 0) {
        $file = $baseDir . 'geoip2/src/' . str_replace('\\', '/', substr($class, 7)) . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
    
    // MaxMind\Db namespace
    if (strpos($class, 'MaxMind\\Db\\') === 0) {
        $file = $baseDir . 'maxmind-db/reader/src/' . str_replace('\\', '/', substr($class, 10)) . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});
PHP;

file_put_contents($autoloaderPath, $autoloader);
echo "✓ Created autoloader\n";

// Update handler.php to include autoloader
$handlerPath = __DIR__ . '/templates/breaking_news/handler.php';
if (file_exists($handlerPath)) {
    $handlerContent = file_get_contents($handlerPath);
    
    // Check if autoloader is already included
    if (strpos($handlerContent, 'vendor/autoload.php') === false) {
        $autoloadInclude = "<?php\n\n// Load MaxMind GeoIP2 library\n";
        $autoloadInclude .= "if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {\n";
        $autoloadInclude .= "    require_once __DIR__ . '/../../vendor/autoload.php';\n";
        $autoloadInclude .= "}\n\n";
        
        // Add after opening PHP tag
        $handlerContent = preg_replace('/^<\?php\n/', $autoloadInclude, $handlerContent);
        file_put_contents($handlerPath, $handlerContent);
        echo "✓ Updated handler.php to include autoloader\n";
    } else {
        echo "✓ handler.php already includes autoloader\n";
    }
}

echo "\n=== Installation Complete ===\n";
echo "The MaxMind GeoIP2 library is now installed!\n";
echo "Location: {$vendorDir}\n\n";

// Test installation
if (file_exists($autoloaderPath)) {
    require_once $autoloaderPath;
    if (class_exists('GeoIp2\Database\Reader')) {
        echo "✓ Library is working correctly!\n";
    } else {
        echo "⚠ Library files may need manual adjustment\n";
    }
}

function rmdir_recursive($dir) {
    if (!is_dir($dir)) return;
    $files = array_diff(scandir($dir), array('.', '..'));
    foreach ($files as $file) {
        $path = $dir . '/' . $file;
        is_dir($path) ? rmdir_recursive($path) : unlink($path);
    }
    rmdir($dir);
}

?>

