import os
import re
import uuid

PUBLIC_DIR = r'g:\Antigravity\leisure_loop_site\public'
CSS_FILE = r'g:\Antigravity\leisure_loop_site\public\css\style.css'  # Add .sr-only here

# Inject .sr-only into style.css
if os.path.exists(CSS_FILE):
    with open(CSS_FILE, 'r', encoding='utf-8') as f:
        css = f.read()
    if '.sr-only' not in css:
        with open(CSS_FILE, 'a', encoding='utf-8') as f:
            f.write("""\n
/* Accessibility Utilities */
.sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}
""")

files = [os.path.join(PUBLIC_DIR, f) for f in os.listdir(PUBLIC_DIR) if os.path.isfile(os.path.join(PUBLIC_DIR, f)) and f.endswith(('.php', '.html'))]

def process_forms(content):
    # 1. CSRF Injection
    # We look for <form ...>
    # If the form doesn't already contain a csrf_token, add it.
    
    # Split by <form> to handle each form block
    parts = re.split(r'(<form[^>]*>)', content, flags=re.IGNORECASE)
    new_content = parts[0]
    
    for i in range(1, len(parts), 2):
        form_tag = parts[i]
        form_body = parts[i+1]
        
        # Inject CSRF token if not present
        if 'name="csrf_token"' not in form_body:
            csrf_input = '\n<input type="hidden" name="csrf_token" value="<?= $_SESSION[\'csrf_token\'] ?? \'\' ?>">\n'
            # Insert right after the form tag
            new_content += form_tag + csrf_input
        else:
            new_content += form_tag
            
        new_content += form_body
        
    return new_content

def process_labels(content):
    # Match inputs and textareas with placeholders
    # We need to parse them, find ID (or generate one), and inject label before it.
    # Because HTML can be multiline, regex might be messy, but we can do a robust search.
    
    # Pattern: match <input or <textarea and capture the whole tag
    tag_pattern = re.compile(r'(<(?:input|textarea)\b[^>]*placeholder=["\']([^"\']+)["\'][^>]*>)', re.IGNORECASE)
    
    def replacer(match):
        full_tag = match.group(1)
        placeholder = match.group(2)
        
        # Check if type="hidden" or type="submit", usually no placeholder, but skip anyway
        if re.search(r'type=["\'](?:hidden|submit|button)["\']', full_tag, re.IGNORECASE):
            return full_tag
            
        # Extract ID
        id_match = re.search(r'\bid=["\']([^"\']+)["\']', full_tag, re.IGNORECASE)
        if id_match:
            input_id = id_match.group(1)
        else:
            input_id = f"input_{uuid.uuid4().hex[:8]}"
            # Inject id into the tag
            # We can put it after the tag name
            if full_tag.startswith('<input'):
                full_tag = full_tag.replace('<input', f'<input id="{input_id}"', 1)
            else:
                full_tag = full_tag.replace('<textarea', f'<textarea id="{input_id}"', 1)
                
        # We need to make sure we don't duplicate labels if one already exists for this ID.
        # But this is a simple replacement so we'll just inject it.
        # Check if a label for this id already exists in the content (rough check)
        if f'for="{input_id}"' not in content and f"for='{input_id}'" not in content:
            label_tag = f'\n<label for="{input_id}" class="sr-only">{placeholder}</label>\n'
            return label_tag + full_tag
        return full_tag

    return tag_pattern.sub(replacer, content)

def process_aria_labels(content):
    # Match icon-only buttons
    # Pattern: <button ...><i class="..."></i></button>
    # Note: might have whitespace
    btn_pattern = re.compile(r'(<button[^>]*>)\s*<(?:i|svg)[^>]*>.*?</(?:i|svg)>\s*</button>', re.IGNORECASE | re.DOTALL)
    
    def replacer(match):
        full_button = match.group(0)
        start_tag = match.group(1)
        
        # If it already has aria-label, skip
        if 'aria-label' in start_tag:
            return full_button
            
        # Determine label based on class or just generic
        # e.g. fa-trash -> Delete, fa-times -> Close
        label = "Action"
        if 'fa-trash' in full_button or 'delete' in full_button.lower():
            label = "Delete"
        elif 'fa-times' in full_button or 'fa-close' in full_button or '&times;' in full_button:
            label = "Close"
        elif 'fa-search' in full_button:
            label = "Search"
        elif 'fa-chevron-left' in full_button:
            label = "Previous"
        elif 'fa-chevron-right' in full_button:
            label = "Next"
            
        new_start = start_tag.replace('<button', f'<button aria-label="{label}"')
        return full_button.replace(start_tag, new_start)
        
    content = btn_pattern.sub(replacer, content)
    
    # Also catch <a ...><i class="..."></i></a> with no text
    a_pattern = re.compile(r'(<a[^>]*>)\s*<(?:i|svg)[^>]*>.*?</(?:i|svg)>\s*</a>', re.IGNORECASE | re.DOTALL)
    def a_replacer(match):
        full_button = match.group(0)
        start_tag = match.group(1)
        if 'aria-label' in start_tag:
            return full_button
        label = "Link"
        if 'facebook' in full_button.lower(): label = "Facebook"
        elif 'instagram' in full_button.lower(): label = "Instagram"
        elif 'twitter' in full_button.lower(): label = "Twitter"
        elif 'youtube' in full_button.lower(): label = "YouTube"
        new_start = start_tag.replace('<a', f'<a aria-label="{label}"')
        return full_button.replace(start_tag, new_start)
    
    content = a_pattern.sub(a_replacer, content)
    return content

for filepath in files:
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
        
    orig = content
    content = process_forms(content)
    content = process_labels(content)
    content = process_aria_labels(content)
    
    # Extra fix for <a>&times;</a> or <button>&times;</button>
    content = re.sub(r'(<(?:a|button)[^>]*>)\s*(?:&times;|✕)\s*</(?:a|button)>', 
                     lambda m: m.group(0) if 'aria-label' in m.group(1) else m.group(0).replace(m.group(1), m.group(1).replace('<' + m.group(1)[1:2].split()[0], '<' + m.group(1)[1:2].split()[0] + ' aria-label="Close"')), 
                     content, flags=re.IGNORECASE)
    
    # Strip any remaining inline event handlers
    content = re.sub(r'\bon(click|focus|blur|change|submit)\s*=\s*["\'][^"\']*["\']', '', content, flags=re.IGNORECASE)
    
    if content != orig:
        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Processed: {os.path.basename(filepath)}")

print("Batch 1 (Public Views) executed.")
