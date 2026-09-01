import re

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    content = f.read()

new_func = '''function initThemeCircleMarquees() {
    function setupDraggableMarquee(rowId, direction) {
        const row = document.getElementById(rowId);
        if (!row) return;

        let isDown = false;
        let startX = 0;
        let startScrollLeft = 0;
        let hasDragged = false;
        let isHovered = false;
        const speed = 0.85; // Auto-scroll speed

        // Prevent native drag on images/anchors
        row.querySelectorAll('img, a').forEach(el => {
            el.addEventListener('dragstart', (e) => e.preventDefault());
        });

        // Initial scroll position setup
        const halfWidth = row.scrollWidth / 2;
        if (direction === 'left' && halfWidth > 0) {
            row.scrollLeft = halfWidth;
        }

        row.addEventListener('mouseenter', () => isHovered = true);
        row.addEventListener('mouseleave', () => {
            isHovered = false;
            isDown = false;
        });

        row.addEventListener('mousedown', (e) => {
            isDown = true;
            hasDragged = false;
            startX = e.pageX - row.offsetLeft;
            startScrollLeft = row.scrollLeft;
        });

        row.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            const x = e.pageX - row.offsetLeft;
            const walk = (x - startX);
            if (Math.abs(walk) > 6) hasDragged = true;
            row.scrollLeft = startScrollLeft - walk;
        });

        row.addEventListener('mouseup', () => { isDown = false; });

        // Prevent unwanted clicks during drag
        row.querySelectorAll('.theme-circle-card-item').forEach(card => {
            card.addEventListener('click', (e) => {
                if (hasDragged) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            });
        });

        // Infinite loop animation loop
        function step() {
            const trackHalf = row.scrollWidth / 2;
            if (!isDown && !isHovered && trackHalf > 0) {
                if (direction === 'left') {
                    row.scrollLeft -= speed;
                } else {
                    row.scrollLeft += speed;
                }
            }

            if (trackHalf > 0) {
                if (row.scrollLeft >= trackHalf) {
                    row.scrollLeft -= trackHalf;
                    if (isDown) startScrollLeft -= trackHalf;
                } else if (row.scrollLeft <= 0) {
                    row.scrollLeft += trackHalf;
                    if (isDown) startScrollLeft += trackHalf;
                }
            }
            requestAnimationFrame(step);
        }
        requestAnimationFrame(step);
    }

    // Row 1 moves to the left, Row 2 moves to the right
    setupDraggableMarquee('circleMarqueeRow1', 'right');
    setupDraggableMarquee('circleMarqueeRow2', 'left');
}
'''

# Add the function definition if it doesn't exist
if 'function initThemeCircleMarquees()' not in content:
    # Insert it right before initAnimations
    content = content.replace('function initAnimations()', new_func + '\nfunction initAnimations()')
    print("Injected function definition.")

# Add the call inside initAnimations if it doesn't exist
if 'initThemeCircleMarquees();' not in content:
    content = content.replace('initCompanyDeck();', 'initCompanyDeck();\n    initThemeCircleMarquees();')
    print("Injected function call.")

with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'w', encoding='utf-8') as f:
    f.write(content)
