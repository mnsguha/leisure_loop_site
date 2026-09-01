import re

with open('G:\\Antigravity\\leisure_loop_site\\public\\js\\modules\\home.js', 'r', encoding='utf-8') as f:
    content = f.read()

# 1. We find the start of initAnimations
start_idx = content.find('function initAnimations() {')
if start_idx == -1:
    print('Could not find initAnimations()')
    exit(1)

# 2. Find the end of initAnimations
# We know it ends before function bootHomePage()
end_idx = content.find('function bootHomePage() {', start_idx)
if end_idx == -1:
    print('Could not find bootHomePage()')
    exit(1)

init_body = content[start_idx:end_idx]

# 3. We will wrap specific sections within initAnimations body.
# Wait, actually, the user wants us to BREAK the monolithic code into isolated functions.
# Let's extract sections based on comments.

def extract_section(text, start_marker, end_marker=None):
    start = text.find(start_marker)
    if start == -1: return ''
    if end_marker:
        end = text.find(end_marker, start)
        if end == -1: return text[start:]
        return text[start:end]
    else:
        return text[start:]

# Instead of complex parsing, let's inject try/catch directly into the file.
# The user asked to create initHeroParallax(), initGlobeAnimation(), initAirplaneFlight(), initCardSliders().
# Let's rewrite the initAnimations function into these calls, and define the functions above it.

# Let's just create a Python script that uses regex to inject if (!target) return; 
# for every GSAP timeline trigger.

import re

# Add try-catch around major sections. This is very risky with regex if we get braces wrong.
# Let's write a targeted replacement script.
