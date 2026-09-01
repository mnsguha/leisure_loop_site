import os

with open('debug_out.html', 'r', encoding='utf-8') as f:
    html = f.read()

# The header ends right before the <style> tags or the <section class="parable-hero">
# The footer begins at <!-- Notice Popup Styles -->
start_marker = '<section class="parable-hero"'
end_marker = '<!-- Notice Popup Styles -->'

start_idx = html.find(start_marker)
end_idx = html.find(end_marker)

if start_idx != -1 and end_idx != -1:
    content = html[start_idx:end_idx]
    
    # We also need the script tags at the bottom!
    # They were right above the Notice Popup Styles, wait no! The notice popup is IN the footer!
    # The footer.php contains the notice popup!
    
    final_php = '<?php \nrequire_once "../config/db.php";\n$page_title = "Sikkim | Elite Travel Experiences";\ninclude "../includes/header.php"; \n?>\n\n'
    
    # Let's find any custom scripts we added that should be before the footer
    script_start = html.find('<script>', start_idx, end_idx)
    # The custom GSAP code for parableTl was inside a script block.
    # Where did it end? It ended before the footer.
    
    final_php += content
    
    # Wait, the script tag for the ScrollTrigger parallax is inside `content`?
    # Let's check where the JS block is!
    # It might be below end_marker? No, it's above the footer.
    
    final_php += '\n\n<?php include "../includes/footer.php"; ?>\n'
    
    with open('public/destinations.php', 'w', encoding='utf-8') as f:
        f.write(final_php)
    print("Success. Length:", len(final_php))
else:
    print("Start:", start_idx, "End:", end_idx)
