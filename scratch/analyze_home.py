with open('G:\\Antigravity\\leisure_loop_site\\public\\js\\modules\\home.js', 'r', encoding='utf-8') as f:
    lines = f.readlines()

def grep(pattern):
    for i, line in enumerate(lines):
        if pattern in line:
            print(f'Line {i+1}: {line.strip()}')

print('--- Searching for animation sections ---')
grep('initAnimations')
grep('parable')
grep('globe')
grep('airplane')
grep('carousel')
grep('hero')
