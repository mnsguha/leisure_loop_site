with open('G:\\Antigravity\\leisure_loop_site\\public\\js\\modules\\home.js', 'r', encoding='utf-8') as f:
    content = f.read()

start = 8892
end = 35587

inside = content[start:end]

# Remove the leading 'document.addEventListener('DOMContentLoaded', function() {'
inside = inside.replace("document.addEventListener('DOMContentLoaded', function() {", "", 1)
# Remove the trailing '});' (the last character is }, but we need to strip exactly what's there)
# Actually, inside goes up to 35587 which is the closing '}'.

replacement = '''function initAnimations() {
    gsap.registerPlugin(ScrollTrigger);
''' + inside[1:] + '''
    ScrollTrigger.refresh();
    console.log('GSAP initialized:', typeof gsap, typeof ScrollTrigger);
}

if (document.readyState === 'complete') {
    initAnimations();
} else {
    window.addEventListener('load', initAnimations);
}
'''

new_content = content[:start] + replacement + content[end+1:] # +1 to skip the closing brace

# Wait, the closing brace is at index end (35587), but what about });?
# The brace is at 35587, the ); is right after it.
new_content = new_content.replace('});\n\n\n    // Generic Event Delegation', '\n\n    // Generic Event Delegation')

with open('G:\\Antigravity\\leisure_loop_site\\public\\js\\modules\\home.js', 'w', encoding='utf-8') as f:
    f.write(new_content)
    
print("Successfully replaced GSAP block.")
