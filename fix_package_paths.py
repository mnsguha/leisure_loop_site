import sys

file_path = "public/package-detail.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

content = content.replace("fetch('../api/submit-package-booking.php'", "fetch('api-submit-package-booking.php'")
content = content.replace("window.open('../api/download-package-inquiry-slip.php?id='", "window.open('api-download-package-inquiry-slip.php?id='")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)

print("Paths updated successfully!")
