import os
import re

ADMIN_DIR = r'g:\Antigravity\leisure_loop_site\public\admin'
JS_FILE = r'g:\Antigravity\leisure_loop_site\public\js\modules\admin-scripts.js'

files = [
    'blog-form.php', 'cab-rates.php', 'cab_class-form.php', 'destination-form.php',
    'hotel-gallery.php', 'hotel-inventory.php', 'hotel-rooms.php', 'hotels.php',
    'leads.php', 'package-form.php', 'package-gallery.php', 'sidebar.php',
    'subscribers.php', 'vehicle-form.php'
]

# Map of onclick exact strings or regexes to their replacements
replacements = [
    (r'onclick="this\.parentElement\.remove\(\)"', r'class="btn-remove js-remove-parent"'),
    (r'onclick="return confirm\(\'(.*?)\'\)[;]*"', r'data-confirm="\1" class="js-confirm-delete"'),
    (r'onclick="toggleNavGroup\(this\)"', r'class="nav-group-header js-toggle-nav"'),
    (r'onclick="toggleBulkDropdown\(\)"', r'class="btn btn-outline js-toggle-bulk-dropdown"'),
    (r'onclick="openBulkModal\(\'inventory\'\)"', r'class="js-open-bulk-modal" data-modal="inventory"'),
    (r'onclick="openBulkModal\(\'rates\'\)"', r'class="js-open-bulk-modal" data-modal="rates"'),
    (r'onclick="closeBulkModal\(\'bulkInventoryModal\'\)"', r'class="js-close-modal" data-target="bulkInventoryModal"'),
    (r'onclick="closeBulkModal\(\'bulkRatesModal\'\)"', r'class="js-close-modal" data-target="bulkRatesModal"'),
    (r'onclick="togglePlanDetails\(<\?php echo \$plan\[\'id\'\]; \?>\)"', r'class="plan-name js-toggle-plan" data-plan-id="<?php echo $plan[\'id\']; ?>"'),
    (r'onclick="document\.getElementById\(\'addPlanModal\'\)\.style\.display=\'flex\';"', r'class="btn-primary js-open-modal" data-target="addPlanModal"'),
    (r'onclick="document\.getElementById\(\'addPlanModal\'\)\.style\.display=\'none\';"', r'class="js-close-modal" data-target="addPlanModal"'),
    (r'onclick="toggleMealPlans\(<\?php echo \$room\[\'id\'\]; \?>\)"', r'class="js-toggle-meal-plans" data-room-id="<?php echo $room[\'id\']; ?>"'),
    (r'onclick="openHotelSelectModal\(\)"', r'class="js-open-hotel-modal"'),
    (r'onclick="closeHotelSelectModal\(\)"', r'class="js-close-hotel-modal"'),
    (r'onclick="selectHotel\(<\?php echo \$h\[\'id\'\]; \?>, \'<\?php echo addslashes\(htmlspecialchars\(\$h\[\'name\'\]\)\); \?>\', event\)"', r'class="hotel-option js-select-hotel" data-hotel-id="<?php echo $h[\'id\']; ?>"'),
    (r'onclick="copyEmails\(\)"', r'class="export-btn js-copy-emails"'),
]

for file in files:
    filepath = os.path.join(ADMIN_DIR, file)
    if not os.path.exists(filepath):
        continue
    
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    original_content = content
    for pattern, repl in replacements:
        # Note: Some elements already have class="...". The naive replace above might duplicate class attributes.
        # It's better to just replace `onclick="..."` with `data-action="..."` and then add class via regex if needed,
        # but let's just do precise replacements to avoid double classes.
        pass

    # A better approach for classes:
    # 1. Replace onclicks with data attributes.
    # 2. In JS, we handle these data attributes.
    
    content = re.sub(r'onclick="this\.parentElement\.remove\(\)"', r'data-action="remove-parent"', content)
    content = re.sub(r'onclick="return confirm\(\'(.*?)\'\)[;]*"', r'data-action="confirm" data-confirm="\1"', content)
    content = re.sub(r'onclick="toggleNavGroup\(this\)"', r'data-action="toggle-nav"', content)
    content = re.sub(r'onclick="toggleBulkDropdown\(\)"', r'data-action="toggle-bulk-dropdown"', content)
    content = re.sub(r'onclick="openBulkModal\(\'inventory\'\)"', r'data-action="open-bulk-modal" data-modal="inventory"', content)
    content = re.sub(r'onclick="openBulkModal\(\'rates\'\)"', r'data-action="open-bulk-modal" data-modal="rates"', content)
    content = re.sub(r'onclick="closeBulkModal\(\'(.*?)\'\)"', r'data-action="close-modal" data-target="\1"', content)
    content = re.sub(r'onclick="togglePlanDetails\((.*?)\)"', r'data-action="toggle-plan" data-plan-id="\1"', content)
    content = re.sub(r'onclick="document\.getElementById\(\'(.*?)\'\)\.style\.display=\'flex\';"', r'data-action="open-modal" data-target="\1"', content)
    content = re.sub(r'onclick="document\.getElementById\(\'(.*?)\'\)\.style\.display=\'none\';"', r'data-action="close-modal" data-target="\1"', content)
    content = re.sub(r'onclick="toggleMealPlans\((.*?)\)"', r'data-action="toggle-meal-plans" data-room-id="\1"', content)
    content = re.sub(r'onclick="openHotelSelectModal\(\)"', r'data-action="open-hotel-modal"', content)
    content = re.sub(r'onclick="closeHotelSelectModal\(\)"', r'data-action="close-hotel-modal"', content)
    content = re.sub(r'onclick="selectHotel\((.*?),\s*\'(.*?)\',\s*event\)"', r'data-action="select-hotel" data-hotel-id="\1"', content)
    content = re.sub(r'onclick="copyEmails\(\)"', r'data-action="copy-emails"', content)
    
    if content != original_content:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Updated onclicks in {file}")

print("Done patching onclicks.")
