from subprocess import getoutput
import requests,json,platform,shutil,os
from modules import control

def find_php_executable():
    """Find PHP executable on Windows or Unix systems"""
    # First, try to find php in PATH
    php_path = shutil.which("php")
    if php_path:
        return php_path
    
    # On Windows, check common installation locations
    if platform.system() == "Windows":
        common_paths = [
            r"C:\xampp\php\php.exe",
            r"C:\wamp64\bin\php\php8.2.0\php.exe",
            r"C:\wamp\bin\php\php8.2.0\php.exe",
            r"C:\php\php.exe",
            r"C:\Program Files\PHP\php.exe",
            r"C:\Program Files (x86)\PHP\php.exe",
        ]
        
        for path in common_paths:
            if os.path.exists(path):
                return path
    
    return None

def dependency():
    php_exe = find_php_executable()
    
    if not php_exe:
        if platform.system() == "Windows":
            print("ERROR: PHP not found!")
            print("Please install PHP from: https://windows.php.net/download/")
            print("Or install XAMPP from: https://www.apachefriends.org/")
            print("\nCommon installation paths:")
            print("  - C:\\xampp\\php\\php.exe")
            print("  - C:\\wamp64\\bin\\php\\php8.2.0\\php.exe")
            print("  - C:\\php\\php.exe")
            print("\nAfter installation, add PHP to your system PATH or restart your terminal.")
        else:
            print("ERROR: PHP not found!")
            print("Please install PHP:")
            print("  Ubuntu/Debian: sudo apt install php")
            print("  CentOS/RHEL: sudo yum install php")
            print("  macOS: brew install php")
        exit(1)
    
    # Verify PHP works
    try:
        check_php = getoutput(f'"{php_exe}" -v')
        if "not found" in check_php.lower() or "error" in check_php.lower():
            raise Exception("PHP check failed")
    except:
        print("ERROR: PHP found but cannot execute. Please check your PHP installation.")
        exit(1)

    try:
        from colorama import Fore,Style
        import requests,psutil

    except ImportError:
        exit("please install library \n command > python3 -m pip install -r requirements.txt")


def check_started():
    with open("nexus-web/Settings.json", "r") as jsonFile:
        data = json.load(jsonFile)

    if data["is_start"] == False:
        data["is_start"] = True
        with open("nexus-web/Settings.json", "w") as jsonFile:
            json.dump(data, jsonFile)



    elif data["is_start"] == True:
        control.kill_php_proc()




def check_update():
    try:
        response = requests.get("https://raw.githubusercontent.com/ultrasecurity/Nexus/main/Settings.json", timeout=5)
        response.raise_for_status()  # Raise an exception for bad status codes
        
        http_json = json.loads(response.text)

        with open("nexus-web/Settings.json", "r") as jsonFile:
            data = json.load(jsonFile)
            if 'version' in data and 'version' in http_json:
                if data['version'] < http_json['version']:
                    exit("Please Update Tool")
    except (requests.RequestException, json.JSONDecodeError, KeyError, FileNotFoundError):
        # Silently skip update check if there's an error (network issue, invalid JSON, etc.)
        pass
