import re

file_path = "g:/Antigravity/leisure_loop_site/public/index.php"
with open(file_path, "r", encoding="utf-8") as f:
    content = f.read()

# Replace font-size: 2.6rem; with font-size: 2.2rem; in the ad banners
# Top banner (home):
content = content.replace("font-size: 2.6rem; color: #ffffff; font-weight: 600; line-height: 1.2; margin: 0; text-shadow: 0 4px 15px rgba(0,0,0,0.8);", 
                          "font-size: 2.1rem; color: #ffffff; font-weight: 600; line-height: 1.35; margin: 0; text-shadow: 0 4px 15px rgba(0,0,0,0.8);")

# Middle banner (home_middle):
# Earlier it was <h3 class="serif" style="font-size: 3rem;... but I replaced it. 
# Oh wait, my python script did content = content.replace("font-size: 3rem;", "font-size: 2.6rem;") globally for 3rem.
content = content.replace("font-size: 2.6rem; color: #ffffff; font-weight: 600; line-height: 1.2;", "font-size: 2.1rem; color: #ffffff; font-weight: 600; line-height: 1.35;")

# Bottom banner (home_bottom):
content = content.replace("font-size: 2.6rem; line-height: 1.2; margin-bottom: 1.5rem; font-weight: 600;", "font-size: 2.1rem; line-height: 1.35; margin-bottom: 1.5rem; font-weight: 600;")

with open(file_path, "w", encoding="utf-8") as f:
    f.write(content)
print("Updated font size to 2.1rem!")
