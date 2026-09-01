with open('G:\\Antigravity\\leisure_loop_site\\public\\js\\modules\\home.js', 'r', encoding='utf-8') as f:
    lines = f.readlines()

in_init = False
for i, line in enumerate(lines):
    if 'function initAnimations()' in line:
        in_init = True
        print(f'{i+1}: {line.strip()}')
        continue
    
    if in_init:
        if 'function bootHomePage()' in line:
            break
        # Print comments and major function calls to see the structure
        line_s = line.strip()
        if line_s.startswith('//') or line_s.startswith('gsap.') or 'const' in line_s or 'let ' in line_s:
            print(f'{i+1}: {line_s[:80]}')
