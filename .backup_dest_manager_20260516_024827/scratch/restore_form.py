import re

path = 'g:/Antigravity/leisure_loop_site/public/index.php'
with open(path, 'r', encoding='utf-8') as f:
    content = f.read()

# Original working form structure (pre-icons)
original_form = """                <form action="api/submit-lead.php" method="POST">
                    <input type="text" name="name" placeholder="Your Full Name" required>
                    <input type="tel" name="phone" placeholder="Phone Number" required>
                    <div class="form-row">
                        <input type="text" name="destination" placeholder="Destination">
                        <input type="text" name="date" placeholder="Travel Date" onfocus="(this.type='date')">
                    </div>
                    <div class="form-row">
                        <select name="adults">
                            <option value="" disabled selected>Adults</option>
                            <option value="1">1 Adult</option>
                            <option value="2">2 Adults</option>
                            <option value="3">3 Adults</option>
                            <option value="4+">4+ Adults</option>
                        </select>
                        <select name="children">
                            <option value="" disabled selected>Children</option>
                            <option value="0">0 Children</option>
                            <option value="1">1 Child</option>
                            <option value="2+">2+ Children</option>
                        </select>
                    </div>
                    <button type="submit" class="btn-card">SUBMIT ENQUIRY &rarr;</button>
                </form>"""

# Find the form area and replace it
content = re.sub(r'<form.*?</form>', original_form, content, flags=re.DOTALL)

with open(path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Form restored to working state.")
