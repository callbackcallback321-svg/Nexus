# Quick Install Guide - MaxMind GeoIP2 Library (Alternative to Composer)

## Method 1: PowerShell Script (Windows - Recommended)

Run this command in PowerShell:

```powershell
cd nexus-web
.\install_geoip2.ps1
```

## Method 2: Manual Download (Works on All Platforms)

### Step 1: Download Libraries

1. **Download GeoIP2 PHP Library:**
   - Visit: https://github.com/maxmind/GeoIP2-php/releases
   - Download: `geoip2-php-2.13.0.zip` (or latest version)
   - Extract the ZIP file

2. **Download MaxMind DB Reader:**
   - Visit: https://github.com/maxmind/MaxMind-DB-Reader-php/releases
   - Download: `MaxMind-DB-Reader-php-1.11.1.zip` (or latest version)
   - Extract the ZIP file

### Step 2: Create Directory Structure

Create these folders in your `nexus-web` directory:

```
nexus-web/
└── vendor/
    └── maxmind/
        ├── geoip2/          (extract GeoIP2-php here)
        └── maxmind-db/
            └── reader/      (extract MaxMind-DB-Reader here)
```

### Step 3: Copy Files

1. Copy the contents of `GeoIP2-php-2.13.0/src/` to `nexus-web/vendor/maxmind/geoip2/src/`
2. Copy the contents of `MaxMind-DB-Reader-php-1.11.1/src/` to `nexus-web/vendor/maxmind/maxmind-db/reader/src/`

### Step 4: Create Autoloader

Create file: `nexus-web/vendor/autoload.php`

```php
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
```

### Step 5: Update handler.php

Add this at the top of `nexus-web/templates/breaking_news/handler.php` (after `<?php`):

```php
// Load MaxMind GeoIP2 library
if (file_exists(__DIR__ . '/../../vendor/autoload.php')) {
    require_once __DIR__ . '/../../vendor/autoload.php';
}
```

## Method 3: Using Git (If Git is Installed)

```bash
cd nexus-web
mkdir -p vendor/maxmind
cd vendor/maxmind

# Clone GeoIP2 library
git clone https://github.com/maxmind/GeoIP2-php.git geoip2

# Clone MaxMind DB Reader
git clone https://github.com/maxmind/MaxMind-DB-Reader-php.git maxmind-db

# Then create autoloader (see Step 4 above)
```

## Verification

After installation, test it:

```bash
php test_geoip.php
```

Or visit the breaking news template - it should automatically use the database!

## Notes

- The library is optional - the system works with API fallback if the library is not installed
- Installing the library provides better accuracy and includes accuracy_radius
- The database file (`geoip.mmdb`) is already in place and ready to use

