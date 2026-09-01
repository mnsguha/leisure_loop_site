import sys, io, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace everything from .leisure-difference-section { to the end of .difference-slider-container
# Actually I'll just find the exact block and replace it using regex to be safe.
import re

pattern = r'\.leisure-difference-section\s*\{.*?(?=\.difference-slider\s*\{)'
match = re.search(pattern, content, re.DOTALL)

if match:
    new_css = '''
.leisure-difference-section {
    position: relative;
    overflow: hidden;
    padding: 8rem 0;
    color: #ffffff;
}

#leisure-difference-bg {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
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
    background: linear-gradient(
        to bottom, 
        rgba(5, 10, 20, 0.95), 
        rgba(5, 10, 20, 0.4), 
        rgba(5, 10, 20, 0.95)
    );
    z-index: 1;
    pointer-events: none;
}

.difference-container {
    position: relative;
    z-index: 2;
}

.difference-slider-container {
    position: relative;
    z-index: 2;
    margin-top: 3rem;
}

'''
    content = content[:match.start()] + new_css + content[match.end():]
    with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'w', encoding='utf-8') as f:
        f.write(content)
    print("Replaced CSS in home.css")
else:
    print("Could not find CSS block")
