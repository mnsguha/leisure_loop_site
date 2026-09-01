import os

file_path = r'g:\Antigravity\leisure_loop_site\public\index.php'

# Read as raw bytes to find the exact sequences
with open(file_path, 'rb') as f:
    raw_data = f.read()

# Replace the specific corrupted byte sequences found in the screenshots
# The "Ã¢Â¦Â" diamond artifact
raw_data = raw_data.replace(b'\xc3\xa2\xc2\xa6\xc2\xa2', b'&#10022;')
raw_data = raw_data.replace(b'\xc3\xa2\xc2\xa6\xc2\xa2', b'&#10022;') # Double check

# The "Ã¢Â‚Â¹" Rupee artifact
raw_data = raw_data.replace(b'\xc3\xa2\xc2\x82\xc2\xb9', b'&#8377;')

# The "Ã¢ÂÂ’" Arrow artifact
raw_data = raw_data.replace(b'\xc3\xa2\xc2\x86\xc2\x92', b'&#8594;')

# General cleanup for any remaining "Ã¢" patterns
raw_data = raw_data.replace(b'\xc3\xa2', b'')
raw_data = raw_data.replace(b'\xc2\xa6', b'')
raw_data = raw_data.replace(b'\xc2\xa2', b'')
raw_data = raw_data.replace(b'\xc2\x82', b'')
raw_data = raw_data.replace(b'\xc2\xb9', b'')
raw_data = raw_data.replace(b'\xc2\x86', b'')
raw_data = raw_data.replace(b'\xc2\x92', b'')

with open(file_path, 'wb') as f:
    f.write(raw_data)

print("Deep cleaning of encoding artifacts complete.")
