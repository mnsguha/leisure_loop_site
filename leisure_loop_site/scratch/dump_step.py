import json
import os

log_path = r'C:\Users\pc\.gemini\antigravity\brain\104e6428-067a-4d2d-98f1-4627e9095a95\.system_generated\logs\overview.txt'

def dump_step(step_index):
    with open(log_path, 'r', encoding='utf-8', errors='ignore') as f:
        for line in f:
            if f'"step_index":{step_index},' in line:
                print(line)
                return

dump_step(724)
