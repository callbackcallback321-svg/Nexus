@echo off
REM GeoIP Database Download Script for Windows
REM Downloads MaxMind GeoLite2-City database

echo === GeoIP Database Setup ===
echo.

set MMDB_PATH=geoip.mmdb

REM Check if file already exists
if exist "%MMDB_PATH%" (
    echo geoip.mmdb already exists
    echo.
    set /p response="Do you want to download a fresh copy? (y/n): "
    if /i not "%response%"=="y" (
        echo Setup cancelled.
        exit /b 0
    )
)

echo Downloading GeoLite2-City database...
echo Note: This is a large file (~50-60 MB). Please be patient.
echo.

REM Try downloading from public mirrors
set MIRROR_URL=https://github.com/P3TERX/GeoLite.mmdb/raw/download/GeoLite2-City.mmdb

echo Trying: %MIRROR_URL%

REM Try using PowerShell to download
powershell -Command "& {[Net.ServicePointManager]::SecurityProtocol = [Net.SecurityProtocolType]::Tls12; Invoke-WebRequest -Uri '%MIRROR_URL%' -OutFile '%MMDB_PATH%' -UseBasicParsing}"

if exist "%MMDB_PATH%" (
    for %%A in ("%MMDB_PATH%") do set SIZE=%%~zA
    if %SIZE% GTR 1000000 (
        echo.
        echo Successfully downloaded geoip.mmdb!
        echo   File location: %CD%\%MMDB_PATH%
        echo.
        echo The GeoIP database is now ready to use.
    ) else (
        echo.
        echo Downloaded file seems too small. Please check the file.
        del "%MMDB_PATH%"
        exit /b 1
    )
) else (
    echo.
    echo Automatic download failed.
    echo.
    echo Please download manually:
    echo 1. Visit: https://www.maxmind.com/en/accounts/current/geoip/downloads
    echo 2. Sign up for a free MaxMind account (if needed)
    echo 3. Download GeoLite2-City.mmdb
    echo 4. Place it at: %CD%\%MMDB_PATH%
    exit /b 1
)

