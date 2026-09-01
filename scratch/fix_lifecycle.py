with open('G:\\Antigravity\\leisure_loop_site\\public\\js\\modules\\home.js', 'r', encoding='utf-8') as f:
    content = f.read()

# First, replace the old readyState logic
old_ready_state = '''if (document.readyState === 'complete') {
    initAnimations();
} else {
    window.addEventListener('load', initAnimations);
}
);'''

# Actually, the file has:
# if (document.readyState === 'complete') {
#     initAnimations();
# } else {
#     window.addEventListener('load', initAnimations);
# }
# );

# Let's find it with regex to be safe.
import re
new_ready_state = '''function bootHomePage() {
    if (typeof gsap !== 'undefined' && typeof ScrollTrigger !== 'undefined') {
        gsap.registerPlugin(ScrollTrigger);
    }
    if (typeof initAnimations === 'function') {
        initAnimations();
    }
    if (typeof bindGlobalEvents === 'function') {
        bindGlobalEvents(); // Bind button and modal listeners immediately!
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', bootHomePage);
} else {
    bootHomePage();
}
window.addEventListener('load', () => {
    if (typeof ScrollTrigger !== 'undefined') ScrollTrigger.refresh();
});
'''
content = re.sub(r"if \(document\.readyState === 'complete'\) \{\s*initAnimations\(\);\s*\} else \{\s*window\.addEventListener\('load', initAnimations\);\s*\}(\s*\);)?", new_ready_state, content)

# Second, wrap the Generic Event Delegation in bindGlobalEvents()
generic_events_start = content.find('// Generic Event Delegation')
if generic_events_start != -1:
    before = content[:generic_events_start]
    after = content[generic_events_start:]
    # Replace the remaining code with a function wrapper
    content = before + "function bindGlobalEvents() {\n" + after + "\n}\n"

with open('G:\\Antigravity\\leisure_loop_site\\public\\js\\modules\\home.js', 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated home.js lifecycle")
