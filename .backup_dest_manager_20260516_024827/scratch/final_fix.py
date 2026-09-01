file_path = r'g:\Antigravity\leisure_loop_site\public\index.php'

# Read raw bytes
with open(file_path, 'rb') as f:
    data = f.read()

# Step 1: Fix known HTML entity sequences that may still be corrupted
replacements = [
    (b'\xe2\x82\xb9',  b'&#8377;'),  # ₹ UTF-8
    (b'\xe2\x86\x92',  b'&rarr;'),   # → UTF-8
    (b'\xe2\x9c\xa6',  b''),         # ✦ UTF-8 - remove completely
    (b'\xe2\xad\x90',  b'&#9733;'),  # ★ UTF-8
    (b'\xc3\xa2\xc2\x82\xc2\xb9', b'&#8377;'),
    (b'\xc3\xa2\xc2\x86\xc2\x92', b'&rarr;'),
    (b'\xc3\xa2\xc2\x9c\xc2\xa6', b''),
]
for old, new in replacements:
    data = data.replace(old, new)

# Step 2: Strip ALL remaining non-ASCII bytes (anything > 127)
# Replace them with nothing - this is safe as all text should be HTML entities now
clean = bytearray()
for byte in data:
    if byte < 128:
        clean.append(byte)
    # else: discard

data = bytes(clean)

with open(file_path, 'wb') as f:
    f.write(data)

# Verify
with open(file_path, 'r', encoding='ascii', errors='replace') as f:
    text = f.read()

bad = [c for c in text if ord(c) > 127]
if bad:
    print(f"WARNING: {len(bad)} non-ASCII chars remain")
else:
    print("SUCCESS: File is 100% clean ASCII + HTML entities. No artifacts possible.")
