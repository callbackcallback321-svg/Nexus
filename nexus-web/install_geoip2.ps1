# MaxMind GeoIP2 Library Installer for Windows
# Alternative to composer - Downloads and sets up the library manually

Write-Host "=== MaxMind GeoIP2 Library Installer ===" -ForegroundColor Cyan
Write-Host ""

$vendorDir = Join-Path $PSScriptRoot "vendor"
$maxmindDir = Join-Path $vendorDir "maxmind"
$geoip2Dir = Join-Path $maxmindDir "geoip2"
$readerDir = Join-Path $maxmindDir "maxmind-db\reader"

# Create directories
if (-not (Test-Path $vendorDir)) {
    New-Item -ItemType Directory -Path $vendorDir -Force | Out-Null
    Write-Host "✓ Created vendor directory" -ForegroundColor Green
}

if (-not (Test-Path $maxmindDir)) {
    New-Item -ItemType Directory -Path $maxmindDir -Force | Out-Null
    Write-Host "✓ Created maxmind directory" -ForegroundColor Green
}

# Download GeoIP2 library
Write-Host "Downloading GeoIP2 library..." -ForegroundColor Yellow
$geoip2Url = "https://github.com/maxmind/GeoIP2-php/archive/refs/tags/v2.13.0.zip"
$geoip2Zip = Join-Path $env:TEMP "GeoIP2-php-2.13.0.zip"

try {
    [Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12
    Invoke-WebRequest -Uri $geoip2Url -OutFile $geoip2Zip -UseBasicParsing
    Expand-Archive -Path $geoip2Zip -DestinationPath $maxmindDir -Force
    
    # Rename extracted folder
    $extracted = Join-Path $maxmindDir "GeoIP2-php-2.13.0"
    if (Test-Path $extracted) {
        if (Test-Path $geoip2Dir) {
            Remove-Item -Path $geoip2Dir -Recurse -Force
        }
        Rename-Item -Path $extracted -NewName "geoip2" -Force
    }
    Remove-Item $geoip2Zip -Force
    Write-Host "✓ GeoIP2 library downloaded and extracted" -ForegroundColor Green
} catch {
    Write-Host "✗ Failed to download GeoIP2 library: $_" -ForegroundColor Red
    Write-Host "  Please download manually from: https://github.com/maxmind/GeoIP2-php/releases" -ForegroundColor Yellow
}

# Download MaxMind DB Reader
Write-Host "Downloading MaxMind DB Reader..." -ForegroundColor Yellow
$readerUrl = "https://github.com/maxmind/MaxMind-DB-Reader-php/archive/refs/tags/v1.11.1.zip"
$readerZip = Join-Path $env:TEMP "MaxMind-DB-Reader-php-1.11.1.zip"

try {
    Invoke-WebRequest -Uri $readerUrl -OutFile $readerZip -UseBasicParsing
    Expand-Archive -Path $readerZip -DestinationPath $maxmindDir -Force
    
    # Rename extracted folder
    $extracted = Join-Path $maxmindDir "MaxMind-DB-Reader-php-1.11.1"
    if (Test-Path $extracted) {
        $readerParent = Split-Path $readerDir
        if (-not (Test-Path $readerParent)) {
            New-Item -ItemType Directory -Path $readerParent -Force | Out-Null
        }
        if (Test-Path $readerDir) {
            Remove-Item -Path $readerDir -Recurse -Force
        }
        Rename-Item -Path $extracted -NewName "maxmind-db" -Force
    }
    Remove-Item $readerZip -Force
    Write-Host "✓ MaxMind DB Reader downloaded and extracted" -ForegroundColor Green
} catch {
    Write-Host "✗ Failed to download MaxMind DB Reader: $_" -ForegroundColor Red
    Write-Host "  Please download manually from: https://github.com/maxmind/MaxMind-DB-Reader-php/releases" -ForegroundColor Yellow
}

# Create autoloader
$autoloaderPath = Join-Path $vendorDir "autoload.php"
$autoloader = @'
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
'@

Set-Content -Path $autoloaderPath -Value $autoloader
Write-Host "✓ Created autoloader" -ForegroundColor Green

# Update handler.php to include autoloader
$handlerPath = Join-Path $PSScriptRoot "templates\breaking_news\handler.php"
if (Test-Path $handlerPath) {
    $handlerContent = Get-Content $handlerPath -Raw
    
    # Check if autoloader is already included
    if ($handlerContent -notmatch 'vendor/autoload\.php') {
        $autoloadInclude = "<?php`n`n// Load MaxMind GeoIP2 library`n"
        $autoloadInclude += "if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {`n"
        $autoloadInclude += "    require_once __DIR__ . '/../../vendor/autoload.php';`n"
        $autoloadInclude += "}`n`n"
        
        # Add after opening PHP tag
        $handlerContent = $handlerContent -replace '^<\?php\n', $autoloadInclude
        Set-Content -Path $handlerPath -Value $handlerContent
        Write-Host "✓ Updated handler.php to include autoloader" -ForegroundColor Green
    } else {
        Write-Host "✓ handler.php already includes autoloader" -ForegroundColor Green
    }
}

Write-Host ""
Write-Host "=== Installation Complete ===" -ForegroundColor Cyan
Write-Host "The MaxMind GeoIP2 library is now installed!"
Write-Host "Location: $vendorDir" -ForegroundColor Green
Write-Host ""

# Test installation
if (Test-Path $autoloaderPath) {
    Write-Host "Testing installation..." -ForegroundColor Yellow
    $testScript = "<?php`nrequire_once '$autoloaderPath';`nif (class_exists('GeoIp2\\Database\\Reader')) {`n    echo 'SUCCESS';`n} else {`n    echo 'FAILED';`n}`n"
    $testFile = Join-Path $env:TEMP "test_geoip2.php"
    Set-Content -Path $testFile -Value $testScript
    $result = php $testFile 2>&1
    Remove-Item $testFile -Force
    
    if ($result -eq "SUCCESS") {
        Write-Host "✓ Library is working correctly!" -ForegroundColor Green
    } else {
        Write-Host "⚠ Library files may need manual adjustment" -ForegroundColor Yellow
    }
}

