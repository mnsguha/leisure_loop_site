with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'r', encoding='utf-8') as f:
    lines = f.readlines()

# Print lines around each orphan block to understand their extent
orphan_lines = [819, 998, 1144, 1536, 1604]
for start in orphan_lines:
    print(f'--- Block starting at line {start} ---')
    for i in range(start-1, min(start+3, len(lines))):
        print(f"  {i+1}: {lines[i].rstrip()}")
    print()
