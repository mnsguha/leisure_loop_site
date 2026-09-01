import os
import json

transcript_path = 'C:/Users/pc/.gemini/antigravity-ide/brain/ceca7355-4877-4352-8239-ad5180eba5a0/.system_generated/logs/transcript_full.jsonl'
with open(transcript_path, 'r', encoding='utf-8') as f:
    for line in f:
        data = json.loads(line)
        if data.get('step_index') in [1211, 1214, 1217, 1218, 1219, 1210]:
            if 'tool_calls' in data:
                for t in data['tool_calls']:
                    if t['name'] == 'replace_file_content' or t['name'] == 'multi_replace_file_content':
                        args = t.get('args', {})
                        if 'TargetContent' in args:
                            print(f"STEP {data.get('step_index')}: TargetContent:\n{args['TargetContent'][:300]}")
                        if 'ReplacementChunks' in args:
                            for c in args['ReplacementChunks']:
                                print(f"STEP {data.get('step_index')}: TargetContent:\n{c.get('TargetContent', '')[:300]}")
