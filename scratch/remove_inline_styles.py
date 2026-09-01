import re
import time

php_path = 'G:/Antigravity/leisure_loop_site/public/destinations.php'
css_path = 'G:/Antigravity/leisure_loop_site/public/css/destinations.css'

with open(php_path, 'r', encoding='utf-8') as f:
    php = f.read()

# 1. .catalog-breadcrumb (already has a class)
php = php.replace(
    '<div class="catalog-breadcrumb" style="justify-content: space-between; flex-wrap: wrap;">',
    '<div class="catalog-breadcrumb catalog-breadcrumb-extended">'
)
# 2. breadcrumb inner div
php = php.replace(
    '<div style="display: flex; align-items: center; gap: 8px;">',
    '<div class="catalog-breadcrumb-links">'
)
# 3. breadcrumb active span
php = php.replace(
    '<span style="color: #fff; font-weight: 500;">',
    '<span class="catalog-breadcrumb-active">'
)
# 4. packages-grid-display
php = php.replace(
    '<main class="packages-grid-display" style="width: 100%;">',
    '<main class="packages-grid-display catalog-grid-main">'
)
# 5. catalog-card-img placeholder
php = php.replace(
    '<div class="catalog-card-img" style="background-color: #0c1828;"></div>',
    '<div class="catalog-card-img catalog-card-img-placeholder"></div>'
)
# 6. catalog-empty-state is handled by style="display: none;". Rule 2 says: "State Management via CSS: Manage UI state transitions, visibility, and modal toggling using CSS classes (e.g., .classList.toggle('is-active')) and ARIA state attributes. You MUST NOT manipulate inline layout styles directly (element.style.display = 'flex' is FORBIDDEN)."
# So we should use a class 'hidden' or 'is-hidden'
php = php.replace(
    '<div id="no-packages-placeholder" class="catalog-empty-state" style="display: none;">',
    '<div id="no-packages-placeholder" class="catalog-empty-state is-hidden">'
)
# 7. empty state svg
php = re.sub(
    r'<svg([^>]+)style="width: 56px; height: 56px; margin: 0 auto 18px auto; opacity:\s*0\.8;">',
    r'<svg\1class="catalog-empty-icon">',
    php
)
# 8. empty state h3
php = php.replace(
    '<h3 style="font-family: \'Playfair Display\', serif; font-size: 1.6rem; color: #fff; margin-bottom: 10px;">',
    '<h3 class="catalog-empty-title">'
)
# 9. empty state p
php = php.replace(
    '<p style="color: rgba(255,255,255,0.7); max-width: 400px; margin: 0 auto 20px;">',
    '<p class="catalog-empty-text">'
)
# 10. empty state button
php = php.replace(
    '<button type="button" data-action="reset-filters" style="background: var(--gold, #C5A059); color: #030811; font-weight: 700; border: none; padding: 10px 24px; border-radius: 30px; cursor: pointer; text-transform: uppercase; letter-spacing: 0.08em; font-size: 0.82rem;">',
    '<button type="button" data-action="reset-filters" class="catalog-empty-btn">'
)

v = str(int(time.time()))
php = re.sub(r'destinations\.css\?v=[0-9]+', f'destinations.css?v={v}', php)

with open(php_path, 'w', encoding='utf-8') as f:
    f.write(php)

css_append = '''
/* Inline Styles Extracted */
.catalog-breadcrumb-extended { justify-content: space-between; flex-wrap: wrap; }
.catalog-breadcrumb-links { display: flex; align-items: center; gap: 8px; }
.catalog-breadcrumb-active { color: #fff; font-weight: 500; }
.catalog-grid-main { width: 100%; }
.catalog-card-img-placeholder { background-color: #0c1828; }
.is-hidden { display: none !important; }
.catalog-empty-icon { width: 56px; height: 56px; margin: 0 auto 18px auto; opacity: 0.8; }
.catalog-empty-title { font-family: 'Playfair Display', serif; font-size: 1.6rem; color: #fff; margin-bottom: 10px; }
.catalog-empty-text { color: rgba(255,255,255,0.7); max-width: 400px; margin: 0 auto 20px; }
.catalog-empty-btn {
    background: var(--gold, #C5A059);
    color: #030811;
    font-weight: 700;
    border: none;
    padding: 10px 24px;
    border-radius: 30px;
    cursor: pointer;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    font-size: 0.82rem;
}
.catalog-empty-btn:hover { background: #fff; }
'''

with open(css_path, 'a', encoding='utf-8') as f:
    f.write(css_append)

print("Inline styles removed and CSS updated.")
