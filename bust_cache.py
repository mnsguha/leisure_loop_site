import os
import glob

admin_dir = "public/admin"
php_files = glob.glob(os.path.join(admin_dir, "*.php"))

for file_path in php_files:
    with open(file_path, "r", encoding="utf-8") as f:
        content = f.read()
    
    if "admin.css" in content and "admin.css?v=" not in content:
        content = content.replace("admin.css\"", "admin.css?v=2\"")
        with open(file_path, "w", encoding="utf-8") as f:
            f.write(content)

print("Cache busted for all admin files.")
