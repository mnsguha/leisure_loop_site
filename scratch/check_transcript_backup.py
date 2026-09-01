import os
import sys

transcript_path = 'C:/Users/pc/.gemini/antigravity-ide/brain/ceca7355-4877-4352-8239-ad5180eba5a0/.system_generated/logs/transcript.jsonl'
if os.path.exists(transcript_path):
    with open(transcript_path, 'r', encoding='utf-8') as f:
        for line in f:
            if 'destinations' in line.lower() and 'backup' in line.lower():
                print(line[:200] + '...')
