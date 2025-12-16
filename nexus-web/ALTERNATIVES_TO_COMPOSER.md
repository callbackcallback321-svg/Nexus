# Alternatives to `composer require maxmind/geoip2`

## Summary

Since you don't have Composer installed, here are **4 alternative methods** to install the MaxMind GeoIP2 library:

---

## ✅ Method 1: Manual Download (Easiest - No Tools Required)

**Best for:** Quick setup without any additional software

1. Download from GitHub:
   - GeoIP2: https://github.com/maxmind/GeoIP2-php/releases
   - DB Reader: https://github.com/maxmind/MaxMind-DB-Reader-php/releases

2. Extract and place files in:
   ```
   nexus-web/vendor/maxmind/geoip2/src/
   nexus-web/vendor/maxmind/maxmind-db/reader/src/
   ```

3. Create autoloader (see `QUICK_INSTALL_GEOIP2.md`)

**Time:** 5-10 minutes

---

## ✅ Method 2: Git Clone (If Git is Installed)

**Best for:** Developers with Git installed

```bash
cd nexus-web
mkdir -p vendor/maxmind
cd vendor/maxmind
git clone https://github.com/maxmind/GeoIP2-php.git geoip2
git clone https://github.com/maxmind/MaxMind-DB-Reader-php.git maxmind-db
```

Then create the autoloader.

**Time:** 2-3 minutes

---

## ✅ Method 3: Use API Fallback (No Installation Needed)

**Best for:** Quick setup, works immediately

**You don't need to install anything!** The system already works with API fallback. The library is optional and only provides:
- Slightly better accuracy
- Accuracy radius field
- Subdivision_2 (county) support

**Current Status:** ✅ Your system is already working with API fallback!

---

## ✅ Method 4: Install Composer (Then Use Original Command)

**Best for:** Long-term development

1. Download Composer: https://getcomposer.org/download/
2. Install it
3. Run: `composer require maxmind/geoip2`

**Time:** 10-15 minutes (one-time setup)

---

## Recommendation

**For now:** Use Method 3 (API Fallback) - it's already working!

**Later:** If you want the extra features, use Method 1 (Manual Download) - it's simple and doesn't require any tools.

---

## Current Status

✅ **GeoIP database file:** Already installed (`geoip.mmdb`)  
✅ **System functionality:** Working with API fallback  
⚠️ **MaxMind library:** Optional - not installed yet

The system is **fully functional** right now. The library installation is optional for enhanced features.

