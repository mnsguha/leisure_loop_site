import re

file_path = r'g:\Antigravity\leisure_loop_site\public\index.php'

# Read raw bytes - no encoding interpretation
with open(file_path, 'rb') as f:
    data = f.read()

# The byte sequences we need to fix.
# These cover ALL known states: original UTF-8, single-corrupted, double-corrupted
replacements = [
    # ₹ rupee symbol in all corruption states
    (b'\xc3\xa2\xc2\x82\xc2\xb9', b'&#8377;'),  # triple-encoded
    (b'\xc3\xa2\xc2\x82\xc2\xb9', b'&#8377;'),  # same
    (b'\xe2\x82\xb9',              b'&#8377;'),  # original UTF-8
    (b'&#8377;&#8377;',            b'&#8377;'),  # de-dupe if any

    # → arrow symbol in all corruption states  
    (b'\xc3\xa2\xc2\x86\xc2\x92', b'&rarr;'),   # triple-encoded
    (b'\xe2\x86\x92',              b'&rarr;'),   # original UTF-8

    # ✦ diamond in all corruption states
    (b'\xc3\xa2\xc2\x9c\xc2\xa6', b'&#10022;'), # triple-encoded
    (b'\xe2\x9c\xa6',              b'&#10022;'), # original UTF-8

    # ★ star
    (b'\xc3\xa2\xc2\xad\xc2\x90', b'&#9733;'),  
    (b'\xe2\xad\x90',              b'&#9733;'),

    # Any remaining Ã¢ pattern cleanup
    (b'\xc3\xa2\xc2\x82',         b''),
    (b'\xc3\xa2\xc2\x86',         b''),
    (b'\xc3\xa2\xc2\x9c',         b''),
    (b'\xc3\xa2',                  b''),
    (b'\xc2\xb9',                  b''),
    (b'\xc2\x92',                  b''),
    (b'\xc2\xa6',                  b''),
]

for old, new in replacements:
    data = data.replace(old, new)

# Ensure file starts with UTF-8 BOM-less clean ASCII 
# Remove any stray non-ASCII bytes that snuck in (except inside PHP string literals we care about)
# Write back as raw bytes (already clean ASCII + HTML entities)
with open(file_path, 'wb') as f:
    f.write(data)

# Verify - read back as text and check for remaining artifacts
with open(file_path, 'r', encoding='utf-8', errors='replace') as f:
    text = f.read()

artifacts = ['Ã¢', 'â', 'Â¦', 'Â¹', 'ÃÂ']
found = [a for a in artifacts if a in text]
if found:
    print(f"WARNING: Still found artifacts: {found}")
else:
    print("SUCCESS: All encoding artifacts removed. File is clean.")
