import os

with open('debug_out.html', 'r', encoding='utf-8') as f:
    html = f.read()

start = html.find('<script src="https://cdn.tailwindcss.com')
end = html.find('<section class="parable-hero"')

if start != -1 and end != -1:
    missing_code = html[start:end]
    
    with open('public/destinations.php', 'r', encoding='utf-8') as f:
        dest = f.read()
    
    # Prepend the missing code right before parable-hero
    hero_pos = dest.find('<section class="parable-hero"')
    
    if hero_pos != -1:
        new_dest = dest[:hero_pos] + missing_code + dest[hero_pos:]
        with open('public/destinations.php', 'w', encoding='utf-8') as f:
            f.write(new_dest)
        print("Successfully restored the missing CSS and Tailwind script!")
    else:
        print("parable-hero not found in destinations.php")
else:
    print("Could not find boundaries in debug_out.html", start, end)
