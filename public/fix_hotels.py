with open('hotels.php', 'r', encoding='utf-8') as f:
    lines = f.readlines()

# Keep lines before 315 (which is index 314) and after 470 (index 470)
new_lines = lines[:314] + lines[470:]

with open('hotels.php', 'w', encoding='utf-8') as f:
    f.writelines(new_lines)
print("Fixed syntax error")
