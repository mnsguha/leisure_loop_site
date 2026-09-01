import os
import json

transcript_path = 'C:/Users/pc/.gemini/antigravity-ide/brain/ceca7355-4877-4352-8239-ad5180eba5a0/.system_generated/logs/transcript_full.jsonl'
with open(transcript_path, 'r', encoding='utf-8') as f:
    for line in f:
        data = json.loads(line)
        if data.get('step_index') in [1211, 1214, 1217, 1218, 1219]:
            if 'tool_calls' in data:
                for t in data['tool_calls']:
                    if t['name'] == 'multi_replace_file_content':
                        args = t.get('args', {})
                        if 'ReplacementChunks' in args:
                            print(f"STEP {data.get('step_index')}:")
                            for chunk in args['ReplacementChunks']:
                                print("TARGET:")
                                print(chunk.get('TargetContent', ''))
