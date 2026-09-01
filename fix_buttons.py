import os

dest_path = 'public/destinations.php'

with open(dest_path, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. We need to move the Modal HTML above the <script> block
# The modal HTML starts with <!-- Full-Screen Cinematic Modal (Option 2) -->
modal_marker = '<!-- Full-Screen Cinematic Modal (Option 2) -->'

if modal_marker in content:
    # Find the modal block
    idx_modal_start = content.find(modal_marker)
    # Find the end of the modal block which is just before footer_inc
    idx_footer = content.find("<?php include '../includes/footer.php'; ?>")
    
    if idx_modal_start != -1 and idx_footer != -1 and idx_modal_start < idx_footer:
        modal_html = content[idx_modal_start:idx_footer]
        
        # Remove modal html from current position
        content = content[:idx_modal_start] + content[idx_footer:]
        
        # Insert modal html above <script>
        idx_script = content.rfind('<script>')
        if idx_script != -1:
            content = content[:idx_script] + modal_html + '\n' + content[idx_script:]

# 2. We need to move ALL the custom button JS INSIDE the window.addEventListener('load') block!
# This guarantees gsap is loaded and all DOM elements exist.
js_start_marker = '// EXPLORE THE STORY Inline Expansion'
js_end_marker = '        }' # End of if (exploreModalBtn && storyModal) { ... }

idx_js_start = content.find(js_start_marker)
if idx_js_start != -1:
    # Find where the JS ends
    idx_js_end = content.find('</script>', idx_js_start)
    if idx_js_end != -1:
        # Extract the JS
        custom_js = content[idx_js_start:idx_js_end].strip()
        
        # Remove the JS from the current location
        content = content[:idx_js_start] + content[idx_js_end:]
        
        # Now find the end of window.addEventListener('load', () => { ... });
        # We know it ends around line 746 with "});\nconst revealObserver"
        idx_load_end = content.find('});\nconst revealObserver')
        if idx_load_end != -1:
            # Inject the custom_js right before "});"
            content = content[:idx_load_end] + '        ' + custom_js + '\n' + content[idx_load_end:]
        else:
            # Fallback if the marker changed
            idx_load_end2 = content.find('});\n        const revealObserver')
            if idx_load_end2 != -1:
                content = content[:idx_load_end2] + '        ' + custom_js + '\n' + content[idx_load_end2:]

with open(dest_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Buttons fixed.")
