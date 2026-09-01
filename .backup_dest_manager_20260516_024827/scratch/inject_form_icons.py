import os
import re

file_path = r'g:\Antigravity\leisure_loop_site\public\index.php'

with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Define icons
icon_user = '<svg viewBox="0 0 24 24"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>'
icon_phone = '<svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.81 12.81 0 0 0 .6 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.6A2 2 0 0 1 22 16.92z"/></svg>'
icon_map = '<svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>'
icon_calendar = '<svg viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>'
icon_users = '<svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>'

# Update Form Fields
content = content.replace('<input type="text" name="name" placeholder="Your Full Name" required>', 
                        f'<div class="input-group">{icon_user}<input type="text" name="name" placeholder="Your Full Name" required></div>')

content = content.replace('<input type="tel" name="phone" placeholder="Phone Number" required>', 
                        f'<div class="input-group">{icon_phone}<input type="tel" name="phone" placeholder="Phone Number" required></div>')

content = content.replace('<input type="text" name="destination" placeholder="Destination">', 
                        f'<div class="input-group">{icon_map}<input type="text" name="destination" placeholder="Destination"></div>')

content = content.replace('<input type="text" name="date" placeholder="Travel Date" onfocus="(this.type=\'date\')">', 
                        f'<div class="input-group">{icon_calendar}<input type="text" name="date" placeholder="Travel Date" onfocus="(this.type=\'date\')"></div>')

# Row with Adults/Children
row_pattern = re.compile(r'<div class="form-row">.*?</div>\s*</div>', re.DOTALL)
new_row = f"""                <div class="form-row">
                    <div class="input-group">
                        {icon_users}
                        <select name="adults">
                            <option value="" disabled selected>Adults</option>
                            <option value="1">1 Adult</option>
                            <option value="2">2 Adults</option>
                            <option value="3">3 Adults</option>
                            <option value="4+">4+ Adults</option>
                        </select>
                    </div>
                    <div class="input-group">
                        {icon_users}
                        <select name="children">
                            <option value="" disabled selected>Children</option>
                            <option value="0">0 Children</option>
                            <option value="1">1 Child</option>
                            <option value="2+">2+ Children</option>
                        </select>
                    </div>
                </div>"""
content = row_pattern.sub(new_row, content)

# Save
with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Form icons and layout updated.")
