import json
import re
import os

log_path = r'C:\Users\pc\.gemini\antigravity\brain\104e6428-067a-4d2d-98f1-4627e9095a95\.system_generated\logs\overview.txt'
cutoff_time = "2026-05-13T15:23:00Z" # 8:53 PM IST

def extract_file_at_time(target_filename):
    print(f"Searching for {target_filename} before {cutoff_time}...")
    best_content = None
    
    with open(log_path, 'r', encoding='utf-8', errors='ignore') as f:
        for line in f:
            if not line.strip().startswith('{'): continue
            try:
                entry = json.loads(line)
            except: continue
            
            created_at = entry.get('created_at', '')
            if created_at > cutoff_time: continue
            
            # Check for view_file response
            # In Antigravity, TOOL_RESPONSE usually has the content in the 'content' field
            if entry.get('type') == 'TOOL_RESPONSE' or entry.get('type') is None:
                content = entry.get('content', '')
                if 'remove the line number, colon, and leading space.' in content and target_filename in content:
                    # This is a view_file response
                    lines = content.split('\n')
                    result = []
                    in_code = False
                    for l in lines:
                        if 'remove the line number, colon, and leading space.' in l:
                            in_code = True
                            continue
                        if 'The above content shows' in l:
                            in_code = False
                            break
                        if in_code:
                            cleaned = re.sub(r'^\d+: ', '', l)
                            result.append(cleaned)
                    if result:
                        best_content = '\n'.join(result)
                        print(f"Found version at {created_at}")

            # Check for write_to_file call
            tool_calls = entry.get('tool_calls', [])
            for tc in tool_calls:
                if tc['name'] == 'write_to_file' and target_filename in tc['args'].get('TargetFile', ''):
                    best_content = tc['args'].get('CodeContent', '')
                    print(f"Found write_to_file at {created_at}")

    return best_content

# Restore index.php
index_content = extract_file_at_time('index.php')
if index_content:
    with open('g:/Antigravity/leisure_loop_site/public/index_restored_v2.php', 'w', encoding='utf-8') as f:
        f.write(index_content)
    print("Created index_restored_v2.php")

# Restore style.css
css_content = extract_file_at_time('style.css')
if css_content:
    with open('g:/Antigravity/leisure_loop_site/public/css/style_restored_v2.css', 'w', encoding='utf-8') as f:
        f.write(css_content)
    print("Created style_restored_v2.css")
