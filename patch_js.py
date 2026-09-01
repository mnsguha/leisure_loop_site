import os

dest_file = 'public/destinations.php'
with open(dest_file, 'r', encoding='utf-8') as f:
    dest_content = f.read()

# Check if script is already there (to avoid duplicates)
if "const revealObserver = new IntersectionObserver" not in dest_content:
    # Read scratch_stitch.html to get the script
    with open('scratch_stitch.html', 'r', encoding='utf-8') as f:
        scratch_content = f.read()
    
    # Extract the script block at the end
    script_start = scratch_content.rfind('<script>')
    script_end = scratch_content.rfind('</script>') + 9
    
    if script_start != -1 and script_end != -1:
        script_block = scratch_content[script_start:script_end]
        
        # Insert it before the footer include
        footer_marker = "<?php include '../includes/footer.php'; ?>"
        if footer_marker in dest_content:
            new_content = dest_content.replace(footer_marker, f"{script_block}\n\n{footer_marker}")
            with open(dest_file, 'w', encoding='utf-8') as f:
                f.write(new_content)
            print("Successfully injected missing JavaScript.")
        else:
            print("Could not find footer marker.")
    else:
        print("Could not find script block in scratch_stitch.html.")
else:
    print("Script already exists in destinations.php.")
