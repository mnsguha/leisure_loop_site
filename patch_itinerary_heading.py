import re

# 1. Update package-form.php
fpath_admin = r'g:\Antigravity\leisure_loop_site\public\admin\package-form.php'
with open(fpath_admin, 'r', encoding='utf-8') as f:
    admin_content = f.read()

# Default value
admin_content = admin_content.replace(
    "'itinerary' => [],",
    "'itinerary' => [],\n    'itinerary_heading' => 'Day-by-Day Journey',"
)

# POST variable
admin_content = admin_content.replace(
    "$highlights = trim($_POST['highlights'] ?? '');",
    "$highlights = trim($_POST['highlights'] ?? '');\n    $itinerary_heading = trim($_POST['itinerary_heading'] ?? 'Day-by-Day Journey');"
)

# SQL queries
admin_content = admin_content.replace(
    "use_destination_terms=?\";",
    "use_destination_terms=?, itinerary_heading=?\";"
)
admin_content = admin_content.replace(
    "use_destination_terms\";",
    "use_destination_terms, itinerary_heading\";"
)
admin_content = admin_content.replace(
    "$insert_placeholders = \"?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?\";",
    "$insert_placeholders = \"?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?\";"
)
admin_content = admin_content.replace(
    "$exclusions, $use_destination_terms];",
    "$exclusions, $use_destination_terms, $itinerary_heading];"
)

# Form input
html_input = """                    <div class="form-group">
                        <label>Itinerary Section Heading</label>
                        <input type="text" name="itinerary_heading" value="<?php echo htmlspecialchars($pkg['itinerary_heading'] ?? 'Day-by-Day Journey'); ?>">
                    </div>
                    <h3 style="margin-bottom: 1rem;">Day-wise Itinerary</h3>"""

admin_content = admin_content.replace(
    """                    <h3 style="margin-bottom: 1rem;">Day-wise Itinerary</h3>""",
    html_input
)

with open(fpath_admin, 'w', encoding='utf-8') as f:
    f.write(admin_content)


# 2. Update package-detail.php
fpath_detail = r'g:\Antigravity\leisure_loop_site\public\package-detail.php'
with open(fpath_detail, 'r', encoding='utf-8') as f:
    detail_content = f.read()

detail_content = detail_content.replace(
    """<h2 class="serif-accent" style="font-size: 2.2rem; margin-bottom: 2.2rem; font-weight: 500;">Day-by-Day Journey</h2>""",
    """<h2 class="serif-accent" style="font-size: 2.2rem; margin-bottom: 2.2rem; font-weight: 500;"><?php echo htmlspecialchars($pkg['itinerary_heading'] ?? 'Day-by-Day Journey'); ?></h2>"""
)

with open(fpath_detail, 'w', encoding='utf-8') as f:
    f.write(detail_content)

print("Patch applied for itinerary_heading")
