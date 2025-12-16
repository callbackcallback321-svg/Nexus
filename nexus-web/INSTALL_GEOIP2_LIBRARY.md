# Installing MaxMind GeoIP2 PHP Library - Alternative Methods

## Method 1: Manual Download (No Composer Required)

### Step 1: Download the Library
1. Visit: https://github.com/maxmind/GeoIP2-php/releases
2. Download the latest release ZIP file (e.g., `geoip2-php-2.13.0.zip`)
3. Extract the ZIP file

### Step 2: Install Dependencies
The GeoIP2 library requires `maxmind-db/reader`:
1. Visit: https://github.com/maxmind/MaxMind-DB-Reader-php/releases
2. Download the latest release ZIP file
3. Extract it

### Step 3: Copy Files to Your Project
Create the following directory structure in your project:

```
nexus-web/
├── vendor/
│   ├── maxmind/
│   │   ├── geoip2/
│   │   │   └── (extract GeoIP2-php files here)
│   │   └── maxmind-db/
│   │       └── reader/
│   │           └── (extract MaxMind-DB-Reader files here)
```

### Step 4: Create Autoloader
Create `nexus-web/vendor/autoload.php`:

```php
<?php
// Simple autoloader for MaxMind libraries
spl_autoload_register(function ($class) {
    $prefix = 'MaxMind\\';
    $base_dir = __DIR__ . '/maxmind/';
    
    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }
    
    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';
    
    if (file_exists($file)) {
        require $file;
    }
});

// Load GeoIP2 namespace
spl_autoload_register(function ($class) {
    if (strpos($class, 'GeoIp2\\') === 0) {
        $file = __DIR__ . '/geoip2/src/' . str_replace('\\', '/', substr($class, 7)) . '.php';
        if (file_exists($file)) {
            require $file;
        }
    }
});
```

## Method 2: Using Git (If Git is Installed)

```bash
cd nexus-web
mkdir -p vendor/maxmind
cd vendor/maxmind

# Clone GeoIP2 library
git clone https://github.com/maxmind/GeoIP2-php.git geoip2

# Clone MaxMind DB Reader
git clone https://github.com/maxmind/MaxMind-DB-Reader-php.git maxmind-db

# Create autoloader (use the code from Method 1, Step 4)
```

## Method 3: Direct Download Script (Windows PowerShell)

Save this as `install_geoip2.ps1` in `nexus-web`:

```powershell
# Create vendor directory
New-Item -ItemType Directory -Force -Path "vendor\maxmind" | Out-Null

# Download GeoIP2 library
Write-Host "Downloading GeoIP2 library..."
$geoip2Url = "https://github.com/maxmind/GeoIP2-php/archive/refs/heads/main.zip"
Invoke-WebRequest -Uri $geoip2Url -OutFile "geoip2-temp.zip"
Expand-Archive -Path "geoip2-temp.zip" -DestinationPath "vendor\maxmind" -Force
Rename-Item -Path "vendor\maxmind\GeoIP2-php-main" -NewName "geoip2" -Force
Remove-Item "geoip2-temp.zip"

# Download MaxMind DB Reader
Write-Host "Downloading MaxMind DB Reader..."
$readerUrl = "https://github.com/maxmind/MaxMind-DB-Reader-php/archive/refs/heads/main.zip"
Invoke-WebRequest -Uri $readerUrl -OutFile "reader-temp.zip"
Expand-Archive -Path "reader-temp.zip" -DestinationPath "vendor\maxmind" -Force
Rename-Item -Path "vendor\maxmind\MaxMind-DB-Reader-php-main" -NewName "maxmind-db" -Force
Remove-Item "reader-temp.zip"

Write-Host "✓ Libraries downloaded!"
Write-Host "Now create the autoloader (see Method 1, Step 4)"
```

## Method 4: Simplified Standalone Version

If you just need basic functionality, you can use a simplified approach by modifying `handler.php` to use the database directly without the full library.

## Quick Setup Script

I'll create an automated installer for you.

