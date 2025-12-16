#!/usr/bin/env python3
"""
Quick test script to verify Nexus templates are set up correctly
"""

import os
import sys

def check_template(template_name):
    """Check if a template exists and has required files"""
    template_path = f"nexus-web/templates/{template_name}"
    
    if not os.path.exists(template_path):
        return False, f"Template folder '{template_name}' not found"
    
    required_files = ["index.html", "handler.php", "result.txt"]
    missing_files = []
    
    for file in required_files:
        file_path = os.path.join(template_path, file)
        if not os.path.exists(file_path):
            missing_files.append(file)
    
    if missing_files:
        return False, f"Missing files: {', '.join(missing_files)}"
    
    return True, "OK"

def main():
    print("=" * 60)
    print("NEXUS TEMPLATES VERIFICATION")
    print("=" * 60)
    print()
    
    # Check all news templates
    templates = [
        "news_location",
        "audio_news", 
        "breaking_news",
        "live_news"
    ]
    
    all_ok = True
    
    for template in templates:
        status, message = check_template(template)
        status_icon = "[OK]" if status else "[FAIL]"
        print(f"{status_icon} {template:20} - {message}")
        if not status:
            all_ok = False
    
    print()
    print("=" * 60)
    
    # Check audio_news specific files
    print("\nChecking audio_news specific files:")
    audio_files = [
        "nexus-web/templates/audio_news/js/_app.js",
        "nexus-web/templates/audio_news/upload.php"
    ]
    
    for file in audio_files:
        exists = os.path.exists(file)
        icon = "[OK]" if exists else "[FAIL]"
        print(f"{icon} {os.path.basename(file):20} - {'Found' if exists else 'Missing'}")
        if not exists:
            all_ok = False
    
    # Check news_location specific files
    print("\nChecking news_location specific files:")
    location_files = [
        "nexus-web/templates/news_location/error.php"
    ]
    
    for file in location_files:
        exists = os.path.exists(file)
        icon = "[OK]" if exists else "[FAIL]"
        print(f"{icon} {os.path.basename(file):20} - {'Found' if exists else 'Missing'}")
        if not exists:
            all_ok = False
    
    print()
    print("=" * 60)
    
    # Check required assets
    print("\nChecking required JavaScript files:")
    js_files = [
        "nexus-web/assets/js/jquery.min.js",
        "nexus-web/assets/js/location.js",
        "nexus-web/assets/js/client.min.js",
        "nexus-web/assets/js/loc.js",
        "nexus-web/assets/js/recorder.js"
    ]
    
    for file in js_files:
        exists = os.path.exists(file)
        icon = "[OK]" if exists else "[FAIL]"
        print(f"{icon} {os.path.basename(file):20} - {'Found' if exists else 'Missing'}")
        if not exists:
            all_ok = False
    
    print()
    print("=" * 60)
    
    if all_ok:
        print("\n[SUCCESS] ALL CHECKS PASSED!")
        print("\nYou can now start the server with: python st.py")
        print("Then access templates at: http://localhost:2525/templates/{template_name}/index.html")
    else:
        print("\n[ERROR] SOME CHECKS FAILED!")
        print("Please fix the missing files before testing.")
    
    print("=" * 60)

if __name__ == "__main__":
    main()

