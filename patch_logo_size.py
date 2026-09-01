import sys

file_path = "public/admin/sidebar.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

content = content.replace("max-height: 50px;", "max-height: 80px; margin-left: -10px;")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

print("Logo size updated!")
