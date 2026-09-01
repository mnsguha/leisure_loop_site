import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    content = f.read()

# Replace the section opening tags with a bulletproof inline version
old_html = '''    <section class="leisure-difference-section">
        <div id="leisure-difference-bg" class="leisure-difference-bg" style="background-image: url('https://images.unsplash.com/photo-1506905925224-162d4666579b?q=80&w=2070&auto=format&fit=crop'); background-size: cover; background-position: center;"></div>'''

new_html = '''    <section class="leisure-difference-section" style="position: relative; overflow: hidden; background: #050a14; z-index: 1;">
        <div id="leisure-difference-bg" class="leisure-difference-bg" style="position: absolute; top: -10%; left: -10%; width: 120%; height: 120%; background-image: url('https://images.unsplash.com/photo-1506905925224-162d4666579b?q=80&w=2070&auto=format&fit=crop'); background-size: cover; background-position: center; z-index: 1; pointer-events: none; transform-origin: center;"></div>
        <div class="difference-overlay" style="position: absolute; inset: 0; background: linear-gradient(to bottom, rgba(5,10,20,0.95) 0%, rgba(5,10,20,0.4) 50%, rgba(5,10,20,0.95) 100%); z-index: 2; pointer-events: none;"></div>'''

if old_html in content:
    content = content.replace(old_html, new_html)
    print("Replaced old_html with new inline bulletproof html.")
else:
    # Try another old html version just in case
    old_html_fallback = '''    <section class="leisure-difference-section">
        <div id="leisure-difference-bg" class="leisure-difference-bg"></div>'''
    if old_html_fallback in content:
        content = content.replace(old_html_fallback, new_html)
        print("Replaced fallback old_html.")
    else:
        print("Couldn't find the exact HTML to replace.")

# We also need to add z-index: 3 to .container so it sits above the overlay
content = content.replace(
    '<div class="container" style="text-align: center; margin-bottom: 4rem;">',
    '<div class="container" style="text-align: center; margin-bottom: 4rem; position: relative; z-index: 3;">'
)
content = content.replace(
    '<div class="container difference-slider-container">',
    '<div class="container difference-slider-container" style="position: relative; z-index: 3;">'
)

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'w', encoding='utf-8') as f:
    f.write(content)
