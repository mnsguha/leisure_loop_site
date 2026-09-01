import sys, io, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

print('=== Final Verification ===')

# index.php checks
with open('G:/Antigravity/leisure_loop_site/public/index.php', 'r', encoding='utf-8') as f:
    php = f.read()

checks = {
    'data-focus eval GONE':      'data-focus' not in php,
    'step2Modal no inline style': 'step2Modal" class="modal-overlay" style=' not in php,
    'mountainBg class applied':  'class="parallax-mountain"' in php,
    'silkContours class applied': 'class="silk-contours-layer"' in php,
    'mountainBg no inline style': 'mountainBg" alt="Mountains" style=' not in php,
    'silkContours no inline style': 'silkContours" style=' not in php,
}
print('index.php:')
for k, v in checks.items():
    print(f'  {"OK" if v else "FAIL"}: {k}')

# home.css checks
with open('G:/Antigravity/leisure_loop_site/public/css/home.css', 'r', encoding='utf-8') as f:
    css = f.read()

print('home.css:')
print(f'  {"OK" if css.count(".flight-anim") == 1 else "FAIL"}: Single flight-anim block')
print(f'  {"OK" if ".parallax-mountain" in css else "FAIL"}: .parallax-mountain class defined')
print(f'  {"OK" if ".silk-contours-layer" in css else "FAIL"}: .silk-contours-layer class defined')

# home.js checks
with open('G:/Antigravity/leisure_loop_site/public/js/modules/home.js', 'r', encoding='utf-8') as f:
    js = f.read()

print('home.js:')
print(f'  {"OK" if "heroDateInput" in js else "FAIL"}: Date input focus listener present')
print(f'  {"OK" if "dateInput.type = " + chr(39) + "date" + chr(39) in js else "FAIL"}: type=date on focus')
print(f'  {"OK" if "dateInput.type = " + chr(39) + "text" + chr(39) in js else "FAIL"}: type=text on empty blur')
