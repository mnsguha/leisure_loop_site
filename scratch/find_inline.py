import os
import re

def find_inline_assets(directory):
    inline_files = []
    
    style_pattern = re.compile(r'<style[^>]*>(.*?)</style>', re.IGNORECASE | re.DOTALL)
    script_pattern = re.compile(r'<script(?![^>]*\bsrc\s*=)[^>]*>(.*?)</script>', re.IGNORECASE | re.DOTALL)

    for root, _, files in os.walk(directory):
        if 'leisure_loop_site\\leisure_loop_site' in root or 'leisure_loop_site/leisure_loop_site' in root:
            continue
        for file in files:
            if file.endswith('.php') or file.endswith('.html'):
                filepath = os.path.join(root, file)
                try:
                    with open(filepath, 'r', encoding='utf-8', errors='ignore') as f:
                        content = f.read()
                        
                        has_style = bool(style_pattern.search(content))
                        
                        # Only consider script if it has some non-whitespace content
                        scripts = script_pattern.findall(content)
                        has_script = any(s.strip() for s in scripts)
                        
                        if has_style or has_script:
                            inline_files.append((filepath, has_style, has_script))
                except Exception as e:
                    print(f"Error reading {filepath}: {e}")
                    
    return inline_files

public_files = find_inline_assets(r'g:\Antigravity\leisure_loop_site\public')
include_files = find_inline_assets(r'g:\Antigravity\leisure_loop_site\includes')

all_files = public_files + include_files

for f, style, script in all_files:
    parts = []
    if style: parts.append('style')
    if script: parts.append('script')
    print(f"{f} - {','.join(parts)}")
