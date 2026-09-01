import re

with open('public/destinations.php', 'r', encoding='utf-8') as f:
    content = f.read()

matches = re.findall(r'<[^>]*class=[^>]*fixed[^>]*>', content)
print("FIXED ELEMENTS:")
for m in matches:
    print(m)

print("\nPOINTER EVENTS NONE CHECK:")
matches2 = re.findall(r'<[^>]*pointer-events[^>]*>', content)
for m in matches2:
    print(m)
