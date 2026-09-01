with open('G:/Antigravity/leisure_loop_site/scratch/init_body.js', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i in range(310, 335):
    if i < len(lines):
        print(f"{i+1}: {lines[i].strip()}")
