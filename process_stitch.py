import re
import urllib.request
import os

html_file = 'scratch_stitch.html'
dest_php_file = 'public/destinations.php'
img_dir = 'public/images/stitch'

if not os.path.exists(img_dir):
    os.makedirs(img_dir)

with open(html_file, 'r', encoding='utf-8') as f:
    content = f.read()

# Find all image URLs starting with https://lh3.googleusercontent.com
urls = re.findall(r'https://lh3\.googleusercontent\.com[^\s"\'<>]+', content)

# Remove duplicates
urls = list(set(urls))

print(f"Found {len(urls)} image URLs to download.")

for i, url in enumerate(urls):
    filename = f"stitch_img_{i+1}.jpg"
    filepath = os.path.join(img_dir, filename)
    local_src = f"images/stitch/{filename}"
    
    print(f"Downloading {filename}...")
    try:
        urllib.request.urlretrieve(url, filepath)
        # Replace the URL in the HTML content with the local path
        content = content.replace(url, local_src)
    except Exception as e:
        print(f"Failed to download {url}: {e}")

with open(dest_php_file, 'w', encoding='utf-8') as f:
    # We should add a minimal PHP header just to be safe, or just output HTML
    f.write(content)

print(f"Successfully generated {dest_php_file}")
