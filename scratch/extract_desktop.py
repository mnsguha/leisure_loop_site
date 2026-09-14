import os

index_path = "G:/Antigravity/leisure_loop_site/public/index.php"
desktop_path = "G:/Antigravity/leisure_loop_site/includes/desktop_home.php"

try:
    with open(index_path, "r", encoding="utf-8") as f:
        lines = f.readlines()
    
    # Lines 122 to 1750 (0-indexed: 121 to 1750)
    desktop_lines = lines[121:1750]
    
    with open(desktop_path, "w", encoding="utf-8") as f:
        f.writelines(desktop_lines)
        
    print("Successfully extracted desktop_home.php")
except Exception as e:
    print(f"Error: {e}")
