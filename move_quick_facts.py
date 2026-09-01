import os

filepath = 'public/destinations.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Find the Quick Facts Bar
qf_start = content.find('<!-- Quick Facts Bar -->')
qf_end = content.find('</div>\n</section>\n<!-- The Narrative')

if qf_start != -1 and qf_end != -1:
    # Extract the block
    # Need to make sure we don't grab the </section> closing tag
    qf_end_actual = content.find('</section>', qf_start)
    
    qf_block = content[qf_start:qf_end_actual].strip()
    
    # Remove it from the original location
    content = content[:qf_start] + content[qf_end_actual:]
    
    # Restyle it to sit nicely below the hero
    # Remove absolute, bottom-0, mb-12, z-40, parallax-layer
    new_qf_block = qf_block.replace('absolute bottom-0 w-full max-w-6xl px-margin-mobile z-40 mb-12 parallax-layer', 'relative max-w-6xl mx-auto px-margin-mobile z-40 -mt-16 mb-16')
    new_qf_block = new_qf_block.replace('data-speed="0.3"', '')
    
    # The new section will be placed right after </section> of the hero
    insertion_point = content.find('<!-- The Narrative (Editorial Section) -->')
    if insertion_point != -1:
        new_content = content[:insertion_point] + new_qf_block + '\n\n' + content[insertion_point:]
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(new_content)
        print("Successfully moved Quick Facts Bar below the Hero Section.")
    else:
        print("Could not find The Narrative section.")
else:
    print("Could not find Quick Facts Bar boundaries.")
