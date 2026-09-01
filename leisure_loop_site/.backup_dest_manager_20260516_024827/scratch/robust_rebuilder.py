import json
import re
import os

log_path = r'C:\Users\pc\.gemini\antigravity\brain\104e6428-067a-4d2d-98f1-4627e9095a95\.system_generated\logs\overview.txt'
start_time = "2026-05-13T12:20:00Z" # Step 188
end_time = "2026-05-13T15:32:00Z"   # 9:02 PM IST

def reconstruct(target_filename, base_content):
    print(f"Reconstructing {target_filename}...")
    current_content = base_content
    
    with open(log_path, 'r', encoding='utf-8', errors='ignore') as f:
        for line in f:
            if not line.strip().startswith('{'): continue
            try:
                entry = json.loads(line)
            except: continue
            
            created_at = entry.get('created_at', '')
            if created_at < start_time or created_at > end_time: continue
            
            tool_calls = entry.get('tool_calls', [])
            for tc in tool_calls:
                if tc['name'] == 'replace_file_content' and target_filename in tc['args'].get('TargetFile', ''):
                    target = tc['args'].get('TargetContent', '')
                    replacement = tc['args'].get('ReplacementContent', '')
                    
                    # Fuzzy match: remove whitespace/newlines for matching
                    def clean(s): return re.sub(r'\s+', '', s)
                    
                    if clean(target) in clean(current_content):
                        # Find the actual target in the current content to preserve formatting
                        # We'll use a simple approach: if exact match fails, try to find the lines
                        if target in current_content:
                            current_content = current_content.replace(target, replacement)
                            print(f"[{created_at}] Applied exact patch: {tc['args'].get('Description', 'No desc')[:30]}")
                        else:
                            # Try to find target by ignoring leading/trailing whitespace per line
                            # This is complex, let's just log it for now
                            print(f"[{created_at}] FAILED exact match for patch: {tc['args'].get('Description', 'No desc')[:30]}")
                
                if tc['name'] == 'write_to_file' and target_filename in tc['args'].get('TargetFile', ''):
                    # If it's a full write, reset the content
                    current_content = tc['args'].get('CodeContent', '')
                    print(f"[{created_at}] Reset content from write_to_file")

    return current_content

# Get base for index.php (Step 188)
index_base = ""
with open(log_path, 'r', encoding='utf-8', errors='ignore') as f:
    for line in f:
        if '"step_index":188,' in line:
            index_base = json.loads(line)['tool_calls'][0]['args']['CodeContent']
            break

# Get base for style.css (Step 178)
css_base = ""
with open(log_path, 'r', encoding='utf-8', errors='ignore') as f:
    for line in f:
        if '"step_index":178,' in line:
            css_base = json.loads(line)['tool_calls'][0]['args']['CodeContent']
            break

final_index = reconstruct('index.php', index_base)
if final_index:
    with open('g:/Antigravity/leisure_loop_site/public/index_final_restore.php', 'w', encoding='utf-8') as f:
        f.write(final_index)

final_css = reconstruct('style.css', css_base)
if final_css:
    with open('g:/Antigravity/leisure_loop_site/public/css/style_final_restore.css', 'w', encoding='utf-8') as f:
        f.write(final_css)
