import os
import re

admin_dir = r"g:\Antigravity\leisure_loop_site\public\admin"

# Regex to find <style> block and everything inside it
style_pattern = re.compile(r'<style>.*?</style>', re.DOTALL)

for filename in os.listdir(admin_dir):
    if filename.endswith(".php"):
        filepath = os.path.join(admin_dir, filename)
        with open(filepath, "r", encoding="utf-8") as f:
            content = f.read()

        # Check if style block exists
        if style_pattern.search(content):
            # Replace the first style block with the admin.css link
            new_content = style_pattern.sub('<link rel="stylesheet" href="../css/admin.css">', content, count=1)
            
            # If the file also had a link to style.css, keep it or remove it?
            # It's okay to keep style.css as it has base fonts, but let's make sure admin.css comes after it.
            # <style> tag usually comes after <link rel="stylesheet" href="../css/style.css">, so the replacement will put admin.css after style.css.
            
            # Handle login.php special case where class="login-card" is inside a wrapper
            if filename == "login.php":
                if '<div class="login-wrapper">' not in new_content:
                    new_content = new_content.replace('<body>\n    <div class="login-card">', '<body class="login-wrapper">\n    <div class="login-card">')

            with open(filepath, "w", encoding="utf-8") as f:
                f.write(new_content)
            print(f"Updated {filename}")
        else:
            # Maybe it doesn't have a style block, just add the link before </head> if not already there
            if '<link rel="stylesheet" href="../css/admin.css">' not in content:
                new_content = content.replace('</head>', '    <link rel="stylesheet" href="../css/admin.css">\n</head>')
                with open(filepath, "w", encoding="utf-8") as f:
                    f.write(new_content)
                print(f"Added admin.css to {filename}")

print("Done updating admin files.")
