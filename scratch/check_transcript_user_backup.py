import os
import json

transcript_path = 'C:/Users/pc/.gemini/antigravity-ide/brain/ceca7355-4877-4352-8239-ad5180eba5a0/.system_generated/logs/transcript.jsonl'
if os.path.exists(transcript_path):
    with open(transcript_path, 'r', encoding='utf-8') as f:
        for line in f:
            if '"USER_INPUT"' in line and 'backup' in line.lower():
                data = json.loads(line)
                print(f"STEP {data.get('step_index')}: {data.get('content')}")
