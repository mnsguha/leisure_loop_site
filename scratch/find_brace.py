with open('G:\\Antigravity\\leisure_loop_site\\public\\js\\modules\\home.js', 'r', encoding='utf-8') as f:
    content = f.read()

start_index = content.find("document.addEventListener('DOMContentLoaded', function() {")
if start_index == -1:
    print('Not found')
    exit(1)

# Find matching brace
brace_count = 0
found_brace = False
for i in range(start_index, len(content)):
    if content[i] == '{':
        brace_count += 1
        found_brace = True
    elif content[i] == '}':
        brace_count -= 1
        if found_brace and brace_count == 0:
            print(f'Closing brace found at index {i}')
            print('Text around it:')
            print(content[i-50:i+50])
            
            # Now let's perform the replacement the user requested!
            # 1. Replace the opening line
            # 2. Add ScrollTrigger.refresh() before the closing brace
            # 3. Add the load listener after the closing brace
            
            replacement = '''
function initAnimations() {
    gsap.registerPlugin(ScrollTrigger);
    // Ensure first video plays
    const firstVideo = document.querySelector('.hero-slide.active video');
    if (firstVideo) {
        firstVideo.play().catch(e => console.log("Autoplay blocked", e));
    }
'''
            
            # The original content inside the block starts after the {
            start_brace_idx = content.find('{', start_index)
            inside_block = content[start_brace_idx + 1 : i]
            
            # Strip the redundant video code and gsap register since we added it to replacement
            inside_block = inside_block.replace('''        // Ensure first video plays
        const firstVideo = document.querySelector('.hero-slide.active video');
        if (firstVideo) {
            firstVideo.play().catch(e => console.log("Autoplay blocked", e));
        }

        // Minimalist Vector Mountain Parallax
        gsap.registerPlugin(ScrollTrigger);''', '')
            
            end_replacement = '''
    ScrollTrigger.refresh();
    console.log('GSAP initialized:', typeof gsap, typeof ScrollTrigger);
}

if (document.readyState === 'complete') {
    initAnimations();
} else {
    window.addEventListener('load', initAnimations);
}
'''
            
            new_content = content[:start_index] + replacement + inside_block + end_replacement + content[i+3:] # +3 to skip '});'
            
            with open('G:\\Antigravity\\leisure_loop_site\\public\\js\\modules\\home.js', 'w', encoding='utf-8') as f:
                f.write(new_content)
                
            print("Successfully rewritten home.js")
            break
