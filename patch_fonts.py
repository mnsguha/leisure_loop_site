import os
import glob

admin_dir = 'public/admin'
files = glob.glob(os.path.join(admin_dir, '*.php'))

font_link = '    <link rel="preconnect" href="https://fonts.googleapis.com">\n    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>\n    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">\n'

for fpath in files:
    with open(fpath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    if '<link href="https://fonts.googleapis.com/css2' not in content and '<head>' in content:
        # Find the title tag or meta tags and insert after
        if '<title>' in content:
            parts = content.split('<title>')
            parts2 = parts[1].split('</title>')
            new_content = parts[0] + '<title>' + parts2[0] + '</title>\n' + font_link + parts2[1]
            
            with open(fpath, 'w', encoding='utf-8') as f:
                f.write(new_content)
            print(f"Patched {fpath}")

print("Done patching fonts.")
