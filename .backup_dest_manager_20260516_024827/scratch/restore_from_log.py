import os
import json
import re

log_path = r'C:\Users\pc\.gemini\antigravity\brain\104e6428-067a-4d2d-98f1-4627e9095a95\.system_generated\logs\overview.txt'

files = {}

target_time = '2026-05-13T15:32:31Z'

with open(log_path, 'r', encoding='utf-8', errors='ignore') as f:
    for line in f:
        line = line.strip()
        if not line.startswith('{'): continue
        try:
            entry = json.loads(line)
        except:
            continue
            
        created_at = entry.get('created_at', '')
        if created_at > target_time:
            break
            
        if entry.get('type') == 'PLANNER_RESPONSE' and 'tool_calls' in entry:
            for tool in entry['tool_calls']:
                args = tool.get('args', {})
                name = tool.get('name')
                
                if name == 'write_to_file':
                    target_file = args.get('TargetFile', '').strip('"').replace('\\', '/')
                    code_content = args.get('CodeContent', '')
                    # Unescape the JSON string properly
                    try:
                        code_content = json.loads(f'"{code_content}"')
                    except:
                        pass
                    files[target_file] = code_content
                    
                elif name == 'replace_file_content':
                    target_file = args.get('TargetFile', '').strip('"').replace('\\', '/')
                    target_content = args.get('TargetContent', '')
                    replacement_content = args.get('ReplacementContent', '')
                    try:
                        target_content = json.loads(f'"{target_content}"')
                        replacement_content = json.loads(f'"{replacement_content}"')
                    except:
                        pass
                        
                    if target_file in files:
                        files[target_file] = files[target_file].replace(target_content, replacement_content)

index_file = [k for k in files.keys() if 'index.php' in k]
style_file = [k for k in files.keys() if 'style.css' in k]

if index_file:
    print(f"Reconstructed {index_file[0]} (length: {len(files[index_file[0]])})")
    with open('g:/Antigravity/leisure_loop_site/public/index_restored.php', 'w', encoding='utf-8') as f:
        f.write(files[index_file[0]])

if style_file:
    print(f"Reconstructed {style_file[0]} (length: {len(files[style_file[0]])})")
    with open('g:/Antigravity/leisure_loop_site/public/css/style_restored.css', 'w', encoding='utf-8') as f:
        f.write(files[style_file[0]])

