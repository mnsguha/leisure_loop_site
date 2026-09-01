import json
import re

log_path = r'C:\Users\pc\.gemini\antigravity\brain\104e6428-067a-4d2d-98f1-4627e9095a95\.system_generated\logs\overview.txt'

def extract(step_index, filename):
    with open(log_path, 'r', encoding='utf-8', errors='ignore') as f:
        for line in f:
            if f'"step_index":{step_index},' in line:
                entry = json.loads(line)
                content = entry.get('content', '')
                # Find content between line numbers
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
                        # remove line number "1: "
                        cleaned = re.sub(r'^\d+: ', '', l)
                        result.append(cleaned)
                
                with open(filename, 'w', encoding='utf-8') as out:
                    out.write('\n'.join(result))
                print(f"Extracted to {filename}")
                return True
    return False

# Step 724 is index.php response
extract(724, 'g:/Antigravity/leisure_loop_site/public/index_v1.php')
# Step 695 is style.css response (around the same time)
extract(695, 'g:/Antigravity/leisure_loop_site/public/css/style_v1.css')
