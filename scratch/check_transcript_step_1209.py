import os
import json

transcript_path = 'C:/Users/pc/.gemini/antigravity-ide/brain/ceca7355-4877-4352-8239-ad5180eba5a0/.system_generated/logs/transcript_full.jsonl'
with open(transcript_path, 'r', encoding='utf-8') as f:
    for line in f:
        data = json.loads(line)
        if data.get('step_index') in [1209, 1210, 1211, 1212, 1213, 1214]:
            if data.get('type') in ['PLANNER_RESPONSE', 'MULTI_REPLACE_FILE_CONTENT']:
                content = data.get('content') or ""
                print(f"STEP {data.get('step_index')}: {content[:200]}")
                if 'tool_calls' in data:
                    for t in data['tool_calls']:
                        if t['name'] == 'multi_replace_file_content':
                            print(t['args'])
