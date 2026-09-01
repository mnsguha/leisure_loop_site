import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

# Check init_body.js for the Art of Discovery animation
with open('G:/Antigravity/leisure_loop_site/scratch/init_body.js', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if 'discovery' in line.lower() or 'process' in line.lower() or 'art-of' in line.lower() or 'process-step' in line.lower():
        for j in range(max(0, i-2), min(len(lines), i+30)):
            print(f'{j+1}: {lines[j].rstrip()}')
        print('---')
        break
