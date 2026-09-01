import re

# 1. Update package-form.php to remove subtitle field
fpath_admin = r'g:\Antigravity\leisure_loop_site\public\admin\package-form.php'
with open(fpath_admin, 'r', encoding='utf-8') as f:
    admin_content = f.read()

# Remove subtitle from PHP processing
admin_content = admin_content.replace(
    "$daySubtitle = trim((string) ($_POST['day_subtitle'][$key] ?? ''));\n",
    ""
)

admin_content = admin_content.replace(
    "'subtitle' => $daySubtitle,\n",
    ""
)

# Remove subtitle from HTML form
html_to_remove = """                            <div class="form-group">
                                <label>Day <?php echo $index + 1; ?> Subtitle</label>
                                <input type="text" name="day_subtitle[]" value="<?php echo htmlspecialchars($day['subtitle'] ?? ''); ?>" placeholder="e.g. The Capital City of Sikkim">
                            </div>"""
admin_content = admin_content.replace(html_to_remove, "")

# Remove subtitle from JavaScript template
js_to_remove = """                <div class="form-group">
                    <label>Day ${dayNum} Subtitle</label>
                    <input type="text" name="day_subtitle[]" placeholder="e.g. The Capital City of Sikkim">
                </div>"""
admin_content = admin_content.replace(js_to_remove, "")

with open(fpath_admin, 'w', encoding='utf-8') as f:
    f.write(admin_content)

# 2. Update package-detail.php to remove subtitle rendering
fpath_detail = r'g:\Antigravity\leisure_loop_site\public\package-detail.php'
with open(fpath_detail, 'r', encoding='utf-8') as f:
    detail_content = f.read()

# Using regex to remove the subtitle block
detail_content = re.sub(
    r'<\?php if \(!empty\(\$day\[\'subtitle\'\]\)\):\ ?>\s*<span class="glass-acc-subtitle">.*?</span>\s*<\?php endif;\ ?>',
    "",
    detail_content,
    flags=re.DOTALL
)

with open(fpath_detail, 'w', encoding='utf-8') as f:
    f.write(detail_content)

print("Patch applied to remove subtitle")
