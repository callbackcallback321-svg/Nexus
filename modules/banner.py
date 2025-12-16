from colorama import Fore,Back,Style
import platform,os

OsName = platform.uname()[0]

def banner():
    if OsName == "Windows":
      os.system("cls")
    else:
      os.system("clear")
    print(Fore.LIGHTWHITE_EX+" _   _  ______  _   _  _   _  ____  ")
    print(Fore.LIGHTWHITE_EX+"| \ | ||  ____|| \ | || | | |/ __ \ ")
    print(Fore.LIGHTWHITE_EX+"|  \| || |__   |  \| || | | || |  | |")
    print(Fore.CYAN+"| . ` ||  __|  | . ` || | | || |  | |")
    print(Fore.CYAN+"| |\  || |____ | |\  || |_| || |__| |")
    print(Fore.CYAN+"|_| \_||______||_| \_| \___/  \____/ ")
    print(Style.RESET_ALL)

banner()