import os

js_path = r'G:\Antigravity\leisure_loop_site\public\js\modules\packages.js'
with open(js_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Encapsulate
content = content.replace("'use strict';", "'use strict';\n\ndocument.addEventListener('DOMContentLoaded', function() {\n")
content += '\n});\n'

# Bind the search button
content = content.replace("function submitLandingSearch() {", "const submitLandingSearch = function() {")

# Add the bindings
bindings = """

    // Event Bindings
    const searchBtn = document.querySelector('[data-action="submit-landing-search"]');
    if (searchBtn) {
        searchBtn.addEventListener('click', submitLandingSearch);
    }
    const searchInput = document.getElementById('search_keyword_input');
    if (searchInput) {
        searchInput.addEventListener('keydown', function(event) {
            if (event.key === 'Enter') {
                submitLandingSearch();
            }
        });
    }
"""
content = content.replace("// Parallax background effect", bindings + "\n    // Parallax background effect")

# We also need to fix filterTrending, filterOffers global calls.
# Let's fix them to be encapsulated and bound via event delegation instead of onclick!
# Wait, let's check if there are inline onclicks in packages.php.
