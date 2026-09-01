import re

# Read index.php to extract the section
with open('public/index.php', 'r', encoding='utf-8') as f:
    index_content = f.read()

# Extract from <!-- Most Coveted Journeys --> to </section> before Film Strip Roll
match = re.search(r'(<!-- Most Coveted Journeys -->.*?</section>)', index_content, re.DOTALL)
if not match:
    print("Section not found in index.php")
    exit()

section_html = match.group(1)

# Fix IDs to avoid collision since both are on the same page
section_html = section_html.replace('id="mostCovetedCarousel"', 'id="mobileCovetedCarousel"')
section_html = section_html.replace("scrollCarousel('mostCovetedCarousel'", "scrollCarousel('mobileCovetedCarousel'")
# Make sure PHP block is at the top of the extracted section (it already is)

# Now read mobile_home.php and insert it before <!-- 7. Hotel Partners -->
with open('includes/mobile_home.php', 'r', encoding='utf-8') as f:
    mobile_content = f.read()

# Insert before <!-- 7. Hotel Partners -->
mobile_content = mobile_content.replace('<!-- 7. Hotel Partners -->', section_html + '\n\n<!-- 7. Hotel Partners -->')

with open('includes/mobile_home.php', 'w', encoding='utf-8') as f:
    f.write(mobile_content)

print("Section brought to mobile_home.php successfully.")
