file_path = r'g:\Antigravity\leisure_loop_site\public\index.php'

with open(file_path, 'r', encoding='ascii', errors='replace') as f:
    text = f.read()

# Fix leading-space placeholders (the ✦ was stripped leaving spaces)
text = text.replace('placeholder="  Your Full Name"', 'placeholder="Your Full Name"')
text = text.replace('placeholder="  Phone Number"',   'placeholder="Phone Number"')

# Fix the Submit button - arrow was stripped, restore it
text = text.replace(
    '>Submit Enquiry \n</button>',
    '>Submit Enquiry &rarr;</button>'
)

# Fix the price display - add rupee entity before the PHP echo
text = text.replace(
    '<span class="price-main"><?php echo number_format($pkg[\'price\']); ?></span>',
    '<span class="price-main">&#8377;<?php echo number_format($pkg[\'price\']); ?></span>'
)
text = text.replace(
    '<span class="price-original">&#8377;<?php echo number_format($pkg[\'original_price\']); ?></span>',
    '<span class="price-original">&#8377;<?php echo number_format($pkg[\'original_price\']); ?></span>'
)
# Add rupee to original price if missing
text = text.replace(
    '<span class="price-original"><?php echo number_format($pkg[\'original_price\']); ?></span>',
    '<span class="price-original">&#8377;<?php echo number_format($pkg[\'original_price\']); ?></span>'
)

with open(file_path, 'w', encoding='ascii') as f:
    f.write(text)

print("Text cleanup complete.")
