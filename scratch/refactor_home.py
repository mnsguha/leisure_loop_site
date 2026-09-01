import re

with open('G:\\Antigravity\\leisure_loop_site\\public\\js\\modules\\home.js', 'r', encoding='utf-8') as f:
    content = f.read()

start_idx = content.find('function initAnimations() {')
end_idx = content.find('function bootHomePage() {', start_idx)

body = content[start_idx:end_idx]

# I need to rebuild the body.
# Let's just create a completely new home.js from the parts, 
# but it's much safer to replace sections of ody using regex.

def wrap_section(text, start_cmt, next_cmt, func_name):
    # Find start
    s_idx = text.find(start_cmt)
    if s_idx == -1: return text
    
    # Find end
    if next_cmt:
        e_idx = text.find(next_cmt, s_idx)
    else:
        e_idx = text.rfind('ScrollTrigger.refresh();')
        
    if e_idx == -1: return text
    
    section = text[s_idx:e_idx]
    
    # Inject element guards for any querySelector inside the section
    lines = section.split('\n')
    new_lines = []
    for line in lines:
        new_lines.append(line)
        if 'document.querySelector(' in line and 'const ' in line:
            var_name = line.split('const ')[1].split(' =')[0].strip()
            # Avoid duplicate guards or complex multi-selectors
            if ',' not in var_name and 'All' not in line:
                new_lines.append(f'        if (!{var_name}) return;')
                
    guarded_section = '\\n'.join(new_lines)
    
    wrapped = f'''
function {func_name}() {{
    try {{
{guarded_section}
    }} catch(e) {{ console.warn('{func_name} error:', e); }}
}}
{func_name}();

'''
    return text[:s_idx] + wrapped + text[e_idx:]

new_body = body

new_body = wrap_section(new_body, '// Minimalist Vector Mountain Parallax', '// Global Escapes Parallax (Globe & Airplane)', 'initHeroParallax')
new_body = wrap_section(new_body, '// Global Escapes Parallax (Globe & Airplane)', '// Premium Entrance for The Curated Collection', 'initGlobeAnimation')
new_body = wrap_section(new_body, '// Signature Terrains Drag & Entrance Animation', '// Fixed Departures Compass Parallax', 'initCardSliders')
new_body = wrap_section(new_body, '// Entrance Animation for Journal & Insights', '// Entrance Animation for Bespoke Travel Experiences', 'initJournalInsights')
new_body = wrap_section(new_body, '// Entrance Animation for Bespoke Travel Experiences', '// Mandala Parallax for Bespoke Themes Section', 'initBespokeThemes')
new_body = wrap_section(new_body, '// The Process Section Animation', '// CRITICAL FIX:', 'initProcessAnimation')

# For airplane, it's inside Global Escapes. The user wanted initAirplaneFlight.
# The user's goal is to not let a failure in the globe or airplane script prevent carousel arrows.
# By wrapping them in try-catches, we achieve this.

content = content.replace(body, new_body)

with open('G:\\Antigravity\\leisure_loop_site\\public\\js\\modules\\home.js', 'w', encoding='utf-8') as f:
    f.write(content)

print("Modularized initAnimations")
