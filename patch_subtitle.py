import re

fpath_admin = r'g:\Antigravity\leisure_loop_site\public\admin\package-form.php'
with open(fpath_admin, 'r', encoding='utf-8') as f:
    admin_content = f.read()

# 1. Update the PHP data extraction
php_old = """            $dayTitle = trim((string) $val);
            $dayDesc = trim((string) ($_POST['day_desc'][$key] ?? ''));
            $dayCoords = trim((string) ($_POST['day_coords'][$key] ?? ''));"""

php_new = """            $dayTitle = trim((string) $val);
            $daySubtitle = trim((string) ($_POST['day_subtitle'][$key] ?? ''));
            $dayDesc = trim((string) ($_POST['day_desc'][$key] ?? ''));
            $dayCoords = trim((string) ($_POST['day_coords'][$key] ?? ''));"""

admin_content = admin_content.replace(php_old, php_new)

php_array_old = """                'title' => $dayTitle,
                'desc' => $dayDesc,
                'coords' => $dayCoords"""

php_array_new = """                'title' => $dayTitle,
                'subtitle' => $daySubtitle,
                'desc' => $dayDesc,
                'coords' => $dayCoords"""

admin_content = admin_content.replace(php_array_old, php_array_new)

# 2. Update the HTML loop
html_old = """                            <div class="form-group">
                                <label>Day <?php echo $index + 1; ?> Title</label>
                                <input type="text" name="day_title[]" value="<?php echo htmlspecialchars($day['title'] ?? ''); ?>" required>
                            </div>"""

html_new = """                            <div class="form-group">
                                <label>Day <?php echo $index + 1; ?> Title</label>
                                <input type="text" name="day_title[]" value="<?php echo htmlspecialchars($day['title'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Day <?php echo $index + 1; ?> Subtitle</label>
                                <input type="text" name="day_subtitle[]" value="<?php echo htmlspecialchars($day['subtitle'] ?? ''); ?>" placeholder="e.g. The Capital City of Sikkim">
                            </div>"""

admin_content = admin_content.replace(html_old, html_new)

# 3. Update the JavaScript template
js_old = """                <div class="form-group">
                    <label>Day ${dayNum} Title</label>
                    <input type="text" name="day_title[]" required>
                </div>"""

js_new = """                <div class="form-group">
                    <label>Day ${dayNum} Title</label>
                    <input type="text" name="day_title[]" required>
                </div>
                <div class="form-group">
                    <label>Day ${dayNum} Subtitle</label>
                    <input type="text" name="day_subtitle[]" placeholder="e.g. The Capital City of Sikkim">
                </div>"""

admin_content = admin_content.replace(js_old, js_new)

with open(fpath_admin, 'w', encoding='utf-8') as f:
    f.write(admin_content)

print("Patch applied for subtitle in admin panel")
