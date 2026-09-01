import sys, io, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

# Add the new CSS classes to home.css
css_addition = '''
/* ---- Parallax Mountain Background ---- */
.parallax-mountain {
    position: absolute;
    bottom: -5%;
    left: 0;
    width: 100%;
    height: auto;
    min-height: 40%;
    object-fit: cover;
    object-position: top;
    z-index: 0;
    pointer-events: none;
    opacity: 0.15;
    filter: grayscale(1) brightness(0.5);
}

/* ---- Silk Contours Parallax Layer ---- */
.silk-contours-layer {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
    pointer-events: none;
    opacity: 0.3;
    overflow: hidden;
}
'''

with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'a', encoding='utf-8') as f:
    f.write(css_addition)

print('home.css: Added .parallax-mountain and .silk-contours-layer classes.')
