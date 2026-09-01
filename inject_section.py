import os

with open('scratch_stitch_2.html', 'r', encoding='utf-8') as f:
    lines = f.readlines()

# Extract lines 368 to 399 (0-indexed, so 369-400)
local_experiences_section = "".join(lines[368:400])

with open('public/destinations.php', 'r', encoding='utf-8') as f:
    dest_content = f.read()

marker = "<!-- Elite Enquiry Form Section -->"
if marker in dest_content:
    new_content = dest_content.replace(marker, f"{local_experiences_section}\n{marker}")
    with open('public/destinations.php', 'w', encoding='utf-8') as f:
        f.write(new_content)
    print("Successfully injected Local Experiences section.")
else:
    # Let's try another marker if that one isn't found
    marker2 = "<!-- Elite Enquiry Form"
    if marker2 in dest_content:
        new_content = dest_content.replace(marker2, f"{local_experiences_section}\n{marker2}")
        with open('public/destinations.php', 'w', encoding='utf-8') as f:
            f.write(new_content)
        print("Successfully injected Local Experiences section using alternative marker.")
    else:
        print("Could not find the insertion marker.")
