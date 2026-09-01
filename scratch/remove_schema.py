import os

def remove_schema_org(filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    idx = content.find('"@context": "https://schema.org"')
    if idx == -1: return
    
    # find the opening brace before this
    start = content.rfind('{', 0, idx)
    
    # find the closing brace by counting
    depth = 0
    end = -1
    for i in range(start, len(content)):
        if content[i] == '{': depth += 1
        elif content[i] == '}':
            depth -= 1
            if depth == 0:
                end = i
                break
                
    if start != -1 and end != -1:
        content = content[:start] + content[end+1:]
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Removed schema block from {filepath}")
        
remove_schema_org('G:/Antigravity/leisure_loop_site/public/js/modules/mobile-views.js')
remove_schema_org('G:/Antigravity/leisure_loop_site/public/js/modules/destinations.js')
