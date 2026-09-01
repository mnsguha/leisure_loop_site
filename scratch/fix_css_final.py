import sys, io, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'r', encoding='utf-8') as f:
    css = f.read()

# Find the start of the section
start_idx = css.find('.leisure-difference-section {')
if start_idx == -1:
    print("Could not find .leisure-difference-section {")
    sys.exit(1)

# Find where to stop. Let's look for .difference-slider {
end_idx = css.find('.difference-slider {', start_idx)
if end_idx == -1:
    print("Could not find .difference-slider {")
    sys.exit(1)

new_css = '''.leisure-difference-section {
    position: relative;
    overflow: hidden;
    padding: 8rem 0;
    color: #ffffff;
    background-color: transparent !important;
}

#leisure-difference-bg {
    position: absolute;
    inset: -10%;
    width: 120%;
    height: 120%;
    background-image: url('https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?q=80&w=1470&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
    will-change: transform;
    pointer-events: none;
    z-index: 0;
    opacity: 1 !important;
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
    z-index: 1;
    pointer-events: none;
}

.difference-container,
.difference-slider-container {
    position: relative;
    z-index: 2;
}

.difference-card {
    background: rgba(5, 10, 20, 0.65) !important;
    backdrop-filter: blur(14px);
    border: 1px solid rgba(197, 160, 89, 0.2) !important;
}

'''
css = css[:start_idx] + new_css + css[end_idx:]
with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'w', encoding='utf-8') as f:
    f.write(css)

print("Successfully replaced CSS.")
