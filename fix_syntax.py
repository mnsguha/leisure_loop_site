import re

with open('public/destinations.php', 'r', encoding='utf-8') as f:
    content = f.read()

# The DOMContentLoaded block is missing its closing "});"
# The last part of the script is:
#         window.addEventListener('scroll', () => { ... });
#     </script>

# We need to add "});" before the </script> for that block
idx = content.find('fog.style.opacity = Math.min(opacity, 0.95);')
if idx != -1:
    end_script = content.find('</script>', idx)
    if end_script != -1:
        # Check if it already has the closing tags
        block_text = content[idx:end_script]
        if '});' in block_text and block_text.count('});') == 1:
            # Only one `});` which is for the scroll listener. We need a second one!
            content = content[:end_script] + '\n        }); // Close DOMContentLoaded\n    ' + content[end_script:]

with open('public/destinations.php', 'w', encoding='utf-8') as f:
    f.write(content)

print("Added missing DOMContentLoaded closing tags.")
