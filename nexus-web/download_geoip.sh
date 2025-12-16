#!/bin/bash

# GeoIP Database Download Script
# Downloads MaxMind GeoLite2-City database

echo "=== GeoIP Database Setup ==="
echo ""

MMDB_PATH="./geoip.mmdb"
MMDB_DIR="."

# Check if file already exists
if [ -f "$MMDB_PATH" ]; then
    echo "✓ geoip.mmdb already exists"
    echo "  File size: $(du -h "$MMDB_PATH" | cut -f1)"
    echo "  Last modified: $(stat -c %y "$MMDB_PATH")"
    echo ""
    read -p "Do you want to download a fresh copy? (y/n): " response
    if [[ ! "$response" =~ ^[Yy]$ ]]; then
        echo "Setup cancelled."
        exit 0
    fi
fi

echo "Downloading GeoLite2-City database..."
echo "Note: This is a large file (~50-60 MB). Please be patient."
echo ""

# Try downloading from public mirrors
MIRROR_URLS=(
    "https://github.com/P3TERX/GeoLite.mmdb/raw/download/GeoLite2-City.mmdb"
    "https://cdn.jsdelivr.net/gh/P3TERX/GeoLite.mmdb@download/GeoLite2-City.mmdb"
)

DOWNLOADED=false

for url in "${MIRROR_URLS[@]}"; do
    echo "Trying: $url"
    
    if wget -O "$MMDB_PATH" "$url" 2>/dev/null || curl -L -o "$MMDB_PATH" "$url" 2>/dev/null; then
        if [ -f "$MMDB_PATH" ] && [ $(stat -f%z "$MMDB_PATH" 2>/dev/null || stat -c%s "$MMDB_PATH" 2>/dev/null) -gt 1000000 ]; then
            echo "✓ Successfully downloaded geoip.mmdb!"
            echo "  File size: $(du -h "$MMDB_PATH" | cut -f1)"
            DOWNLOADED=true
            break
        else
            rm -f "$MMDB_PATH"
            echo "✗ Download failed or file too small."
        fi
    else
        echo "✗ Download failed from this source."
    fi
done

if [ "$DOWNLOADED" = false ]; then
    echo ""
    echo "✗ Automatic download failed."
    echo ""
    echo "Please download manually:"
    echo "1. Visit: https://www.maxmind.com/en/accounts/current/geoip/downloads"
    echo "2. Sign up for a free MaxMind account (if needed)"
    echo "3. Download GeoLite2-City.mmdb"
    echo "4. Place it at: $MMDB_PATH"
    echo ""
    echo "Or use MaxMind's GeoIP Update tool:"
    echo "  https://github.com/maxmind/geoipupdate"
    exit 1
fi

# Verify the file
if [ -f "$MMDB_PATH" ]; then
    FILE_SIZE=$(stat -f%z "$MMDB_PATH" 2>/dev/null || stat -c%s "$MMDB_PATH" 2>/dev/null)
    if [ "$FILE_SIZE" -gt 1000000 ]; then
        echo ""
        echo "✓ Setup complete!"
        echo "  File location: $MMDB_PATH"
        echo "  File size: $(du -h "$MMDB_PATH" | cut -f1)"
        echo ""
        echo "The GeoIP database is now ready to use."
    else
        echo ""
        echo "✗ Downloaded file seems too small. Please check the file."
        exit 1
    fi
else
    echo ""
    echo "✗ File not found. Setup failed."
    exit 1
fi

