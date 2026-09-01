import os
import glob

root_dir = r"g:\Antigravity\leisure_loop_site"

php_files = glob.glob(os.path.join(root_dir, "**/*.php"), recursive=True)
count = 0

for file in php_files:
    try:
        with open(file, "r", encoding="utf-8") as f:
            content = f.read()
        
        if "logo.webp" in content or "logo.png" in content:
            new_content = content.replace("logo.webp", "leisure.png").replace("logo.png", "leisure.png")
            
            with open(file, "w", encoding="utf-8") as f:
                f.write(new_content)
            count += 1
            print(f"Updated {file}")
    except Exception as e:
        print(f"Error reading {file}: {e}")

print(f"Total files updated: {count}")
