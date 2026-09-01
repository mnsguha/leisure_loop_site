import sys, io, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'r', encoding='utf-8') as f:
    css = f.read()

pattern = r'\.leisure-difference-section\s*\{.*?(?=\.difference-slider\s*\{)'
match = re.search(pattern, css, re.DOTALL)

if match:
    new_css = '''/* ====================================
   The Leisure Loop Difference Section
   ==================================== */
.leisure-difference-section {
    position: relative;
    z-index: 1;
    overflow: hidden;
    padding: 8rem 0;
    color: #ffffff;
    background-color: #050a14 !important;
}

#leisure-difference-bg {
    position: absolute;
    inset: -10%;
    width: 120%;
    height: 120%;
    background-image: url('https://images.unsplash.com/photo-1506905925224-162d4666579b?q=80&w=2070&auto=format&fit=crop');
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
    will-change: transform;
    pointer-events: none;
    z-index: 1;
    opacity: 0.65;
}

.difference-overlay {
    position: absolute;
    inset: 0;
    background: linear-gradient(
        to right,
        rgba(5, 10, 20, 0.88),
        rgba(5, 10, 20, 0.5),
        rgba(5, 10, 20, 0.88)
    );
    z-index: 2;
    pointer-events: none;
}

.difference-container,
.difference-slider-container {
    position: relative;
    z-index: 3;
}

.difference-card {
    background: rgba(5, 10, 20, 0.65) !important;
    backdrop-filter: blur(14px);
    border: 1px solid rgba(197, 160, 89, 0.2) !important;
}

'''
    css = css[:match.start()] + new_css + css[match.end():]
    with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'w', encoding='utf-8') as f:
        f.write(css)
    print("Replaced CSS block successfully.")
else:
    print("Could not find CSS block.")
