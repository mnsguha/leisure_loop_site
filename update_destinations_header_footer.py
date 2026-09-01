import os

filepath = 'public/destinations.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Extract styles and tailwind config
start_scripts = content.find('<script src="https://cdn.tailwindcss.com')
end_scripts = content.find('</head>')
if start_scripts != -1 and end_scripts != -1:
    head_content = content[start_scripts:end_scripts].strip()
else:
    head_content = ""

# 2. Extract main body content
start_hero = content.find('<!-- Hero Section -->')
end_footer = content.find('<!-- Footer Shell -->')

if start_hero != -1 and end_footer != -1:
    body_content = content[start_hero:end_footer].strip()
else:
    print("Could not find Hero or Footer boundaries.")
    exit(1)

# 3. Construct new file
new_content = f"""<?php 
require_once '../config/db.php';
$page_title = "Sikkim | Elite Travel Experiences";
include '../includes/header.php'; 
?>

{head_content}

<div class="font-body-md text-body-md overflow-x-hidden bg-background w-full">
{body_content}
</div>

<?php include '../includes/footer.php'; ?>
"""

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(new_content)

print("Successfully updated destinations.php with standard header and footer.")
