import json
import re

log_path = r'C:\Users\pc\.gemini\antigravity\brain\104e6428-067a-4d2d-98f1-4627e9095a95\.system_generated\logs\overview.txt'

target_time = '2026-05-13T15:35:00Z'

last_index_view_step = -1
last_style_view_step = -1

# Find the step index of the last view_file
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
                name = tool.get('name')
                if name == 'view_file':
                    args = tool.get('args', {})
                    path = args.get('AbsolutePath', '')
                    if 'index.php' in path:
                        last_index_view_step = entry['step_index']
                    elif 'style.css' in path:
                        last_style_view_step = entry['step_index']

print(f"Last index view step: {last_index_view_step}")
print(f"Last style view step: {last_style_view_step}")

def extract_file_from_step(target_step_index, output_filename):
    if target_step_index == -1: return
    
    with open(log_path, 'r', encoding='utf-8', errors='ignore') as f:
        for line in f:
            line = line.strip()
            if not line.startswith('{'): continue
            try:
                entry = json.loads(line)
            except:
                continue
                
            if entry.get('step_index') == target_step_index + 1: # The response is usually the next step
                if entry.get('type') == 'TOOL_RESPONSE':
                    output = entry.get('content', '')
                    # Extract the lines between "The following code has been modified..." and "The above content shows..."
                    match = re.search(r'remove the line number, colon, and leading space\.(.*?)(?:The above content|$)', output, re.DOTALL)
                    if match:
                        file_lines = match.group(1).strip().split('\n')
                        cleaned_lines = []
                        for l in file_lines:
                            # remove the line number prefix e.g., "1: "
                            cleaned_l = re.sub(r'^\d+: ', '', l)
                            cleaned_lines.append(cleaned_l)
                        
                        with open(output_filename, 'w', encoding='utf-8') as out_f:
                            out_f.write('\n'.join(cleaned_lines))
                        print(f"Extracted {output_filename}")
                    return

extract_file_from_step(last_index_view_step, 'g:/Antigravity/leisure_loop_site/scratch/index_from_log.php')
extract_file_from_step(last_style_view_step, 'g:/Antigravity/leisure_loop_site/scratch/style_from_log.css')

