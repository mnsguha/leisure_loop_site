import os
import json
import re

transcript_path = 'C:/Users/pc/.gemini/antigravity-ide/brain/ceca7355-4877-4352-8239-ad5180eba5a0/.system_generated/logs/transcript.jsonl'
with open(transcript_path, 'r', encoding='utf-8') as f:
    for line in f:
        if 'destinations.php' in line and '"type":"USER_INPUT"' in line:
            data = json.loads(line)
            print("USER INPUT:", data.get('content')[:300])
