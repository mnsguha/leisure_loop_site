import json
import re
import os

log_path = r'C:\Users\pc\.gemini\antigravity\brain\104e6428-067a-4d2d-98f1-4627e9095a95\.system_generated\logs\overview.txt'
cutoff_time = "2026-05-13T15:23:00Z"

def reconstruct(target_filename):
    print(f"Reconstructing {target_filename}...")
    current_content = None
    
    with open(log_path, 'r', encoding='utf-8', errors='ignore') as f:
        for line in f:
            if not line.strip().startswith('{'): continue
            try:
                entry = json.loads(line)
            except: continue
            
            created_at = entry.get('created_at', '')
            if created_at > cutoff_time: continue
            
            # 1. Look for full content (write_to_file or view_file)
            # Check for view_file response
            if entry.get('type') == 'TOOL_RESPONSE' or entry.get('type') is None:
                content = entry.get('content', '')
                if 'remove the line number, colon, and leading space.' in content and target_filename in content:
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
                        current_content = '\n'.join(result)
                        print(f"[{created_at}] Base established from view_file")

            # Check for write_to_file call
            tool_calls = entry.get('tool_calls', [])
            for tc in tool_calls:
                if tc['name'] == 'write_to_file' and target_filename in tc['args'].get('TargetFile', ''):
                    current_content = tc['args'].get('CodeContent', '')
                    print(f"[{created_at}] Base established from write_to_file")
                
                # 2. Look for patches (replace_file_content)
                if tc['name'] == 'replace_file_content' and target_filename in tc['args'].get('TargetFile', ''):
                    if current_content is None:
                        print(f"[{created_at}] Warning: replace_file_content found but no base content yet!")
                        continue
                    
                    target = tc['args'].get('TargetContent', '')
                    replacement = tc['args'].get('ReplacementContent', '')
                    
                    if target in current_content:
                        current_content = current_content.replace(target, replacement)
                        print(f"[{created_at}] Applied patch: {tc['args'].get('Description', 'No desc')[:50]}...")
                    else:
                        print(f"[{created_at}] Error: Target content not found for patch!")
                        # Try a fuzzy match if needed? No, let's keep it exact for now.

    return current_content

# Restore index.php
final_index = reconstruct('index.php')
if final_index:
    with open('g:/Antigravity/leisure_loop_site/public/index_853pm.php', 'w', encoding='utf-8') as f:
        f.write(final_index)
    print("Created index_853pm.php")

# Restore style.css
final_css = reconstruct('style.css')
if final_css:
    with open('g:/Antigravity/leisure_loop_site/public/css/style_853pm.css', 'w', encoding='utf-8') as f:
        f.write(final_css)
    print("Created style_853pm.css")
