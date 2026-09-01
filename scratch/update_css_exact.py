import sys, io, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace the leisure-difference CSS block
pattern = r'\.leisure-difference-section\s*\{.*?(?=\.difference-container\s*\{)'
match = re.search(pattern, content, re.DOTALL)

if match:
    new_css = '''/* ====================================
   The Leisure Loop Difference Section
   ==================================== */
.leisure-difference-section {
    position: relative;
    overflow: hidden;
    padding: 8rem 0;
    color: #ffffff;
    background-color: #030811;
}

#leisure-difference-bg {
    position: absolute;
    top: -10%;
    left: -5%;
    width: 110%;
    height: 120%;
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    will-change: transform;
    pointer-events: none;
    z-index: 0;
}

.difference-overlay {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at center, rgba(5, 10, 20, 0.55) 0%, rgba(5, 10, 20, 0.88) 100%),
                linear-gradient(to bottom, rgba(5, 10, 20, 0.9) 0%, transparent 40%, rgba(5, 10, 20, 0.95) 100%);
    z-index: 1;
    pointer-events: none;
}

'''
    content = content[:match.start()] + new_css + content[match.end():]
    with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'w', encoding='utf-8') as f:
        f.write(content)
    print("Replaced CSS in home.css")
else:
    print("Could not find CSS block in home.css")

