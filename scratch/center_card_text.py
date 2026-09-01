import sys, io, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'r', encoding='utf-8') as f:
    css = f.read()

# Add text-align: center; to the first .difference-card block
old_block_1 = '''.difference-card {
    background: rgba(5, 10, 20, 0.65) !important;
    backdrop-filter: blur(14px);
    border: 1px solid rgba(197, 160, 89, 0.2) !important;
}'''

new_block_1 = '''.difference-card {
    background: rgba(5, 10, 20, 0.65) !important;
    backdrop-filter: blur(14px);
    border: 1px solid rgba(197, 160, 89, 0.2) !important;
    text-align: center;
}'''

if old_block_1 in css:
    css = css.replace(old_block_1, new_block_1)
    with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'w', encoding='utf-8') as f:
        f.write(css)
    print("Added text-align: center; successfully.")
else:
    print("Could not find exact block to replace.")
