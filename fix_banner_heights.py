import re

file_path = "g:/Antigravity/leisure_loop_site/public/index.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# 1. Update Top Banner & Middle Banner min-height and padding
# Look for: min-height: 320px;
content = content.replace("min-height: 320px;", "min-height: 250px;")
# Look for the padding on the inner div: padding: 4rem; max-width: 60%;
content = content.replace("padding: 4rem; max-width: 60%;", "padding: 2.5rem 4rem; max-width: 60%;")

# 2. Update Bottom Banner padding
# Look for: padding: 5rem 4rem; position: relative; overflow: hidden; border: 1px solid rgba(255,255,255,0.05); box-shadow: 0 20px 40px rgba(0,0,0,0.3);"
content = content.replace("padding: 5rem 4rem; position: relative;", "padding: 3rem 4rem; position: relative;")

# 3. Reduce margin-bottom on the logo pill in the banners to make it tighter
content = content.replace("margin-bottom: 2rem; display: inline-block;", "margin-bottom: 1.2rem; display: inline-block;")

# The top and middle banners have: margin-bottom: -0.5rem; display: inline-block;
# We can change the gap from 1.5rem to 1rem
content = content.replace("gap: 1.5rem;", "gap: 0.8rem;")

# Reduce the title font size slightly to fit the sleeker height and reduce margin
# For bottom banner: margin-bottom: 2.5rem; -> margin-bottom: 1.5rem;
content = content.replace("margin-bottom: 2.5rem; font-weight: 600;", "margin-bottom: 1.5rem; font-weight: 600;")
content = content.replace("font-size: 3rem;", "font-size: 2.6rem;")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated banner heights!")
