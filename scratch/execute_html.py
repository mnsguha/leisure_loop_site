import sys, io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    content = f.read()

# I will replace the previously inlined HTML with the clean HTML.
# Let's find the section block.
start_str = '<section class="leisure-difference-section"'
end_str = 'THE LEISURE LOOP DIFFERENCE</span>'

start_idx = content.find(start_str)
end_idx = content.find(end_str)

if start_idx != -1 and end_idx != -1:
    old_block = content[start_idx:end_idx]
    
    new_block = '''<section class="leisure-difference-section">
        <div id="leisure-difference-bg" style="background-image: url('https://images.unsplash.com/photo-1506905925224-162d4666579b?q=80&w=2070&auto=format&fit=crop');"></div>
        <div class="difference-overlay"></div>
        <div class="container difference-container" style="text-align: center; margin-bottom: 4rem;">
            <span class="section-label-gold" style="letter-spacing: 0.25em;">'''
            
    content = content.replace(old_block, new_block)
    print("Replaced index.php HTML.")
else:
    print("Could not find HTML block.")
    
with open('G:/Antigravity/leisure_loop_site/public/index.php', 'w', encoding='utf-8') as f:
    f.write(content)

