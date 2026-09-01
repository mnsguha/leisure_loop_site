import os
import re
import uuid

INCLUDES_DIR = r'g:\Antigravity\leisure_loop_site\includes'
ADMIN_DIR = r'g:\Antigravity\leisure_loop_site\public\admin'

include_files = [os.path.join(INCLUDES_DIR, f) for f in os.listdir(INCLUDES_DIR) if f.endswith(('.php', '.html'))]
admin_files = [os.path.join(ADMIN_DIR, f) for f in os.listdir(ADMIN_DIR) if f.endswith(('.php', '.html'))]

def process_csrf(content, is_admin=False):
    parts = re.split(r'(<form[^>]*>)', content, flags=re.IGNORECASE)
    new_content = parts[0]
    
    for i in range(1, len(parts), 2):
        form_tag = parts[i]
        form_body = parts[i+1]
        
        # Unify action to /api/v1/leads for NON-admin forms if it looks like a lead form
        if not is_admin:
            if re.search(r'action=["\'](?:api/)?submit-lead\.php["\']', form_tag, re.IGNORECASE):
                form_tag = re.sub(r'action=["\'][^"\']+["\']', 'action="/api/v1/leads"', form_tag, flags=re.IGNORECASE)
        
        # Inject CSRF token if not present
        if 'name="csrf_token"' not in form_body and 'name=\'csrf_token\'' not in form_body:
            csrf_input = '\n<input type="hidden" name="csrf_token" value="<?= $_SESSION[\'csrf_token\'] ?? \'\' ?>">\n'
            new_content += form_tag + csrf_input
        else:
            new_content += form_tag
            
        new_content += form_body
        
    return new_content

def process_labels(content):
    tag_pattern = re.compile(r'(<(?:input|textarea)\b[^>]*placeholder=["\']([^"\']+)["\'][^>]*>)', re.IGNORECASE)
    def replacer(match):
        full_tag = match.group(1)
        placeholder = match.group(2)
        if re.search(r'type=["\'](?:hidden|submit|button)["\']', full_tag, re.IGNORECASE):
            return full_tag
            
        id_match = re.search(r'\bid=["\']([^"\']+)["\']', full_tag, re.IGNORECASE)
        if id_match:
            input_id = id_match.group(1)
        else:
            input_id = f"input_{uuid.uuid4().hex[:8]}"
            if full_tag.startswith('<input'):
                full_tag = full_tag.replace('<input', f'<input id="{input_id}"', 1)
            else:
                full_tag = full_tag.replace('<textarea', f'<textarea id="{input_id}"', 1)
                
        if f'for="{input_id}"' not in content and f"for='{input_id}'" not in content:
            label_tag = f'\n<label for="{input_id}" class="sr-only">{placeholder}</label>\n'
            return label_tag + full_tag
        return full_tag
    return tag_pattern.sub(replacer, content)

def process_aria_labels(content):
    btn_pattern = re.compile(r'(<button[^>]*>)\s*<(?:i|svg)[^>]*>.*?</(?:i|svg)>\s*</button>', re.IGNORECASE | re.DOTALL)
    def replacer(match):
        full_button = match.group(0)
        start_tag = match.group(1)
        if 'aria-label' in start_tag: return full_button
        label = "Action"
        low = full_button.lower()
        if 'trash' in low or 'delete' in low: label = "Delete"
        elif 'times' in low or 'close' in low or '&times;' in low: label = "Close"
        elif 'search' in low: label = "Search"
        elif 'edit' in low or 'pen' in low: label = "Edit"
        new_start = start_tag.replace('<button', f'<button aria-label="{label}"')
        return full_button.replace(start_tag, new_start)
    content = btn_pattern.sub(replacer, content)
    
    a_pattern = re.compile(r'(<a[^>]*>)\s*<(?:i|svg)[^>]*>.*?</(?:i|svg)>\s*</a>', re.IGNORECASE | re.DOTALL)
    def a_replacer(match):
        full_button = match.group(0)
        start_tag = match.group(1)
        if 'aria-label' in start_tag: return full_button
        label = "Link"
        low = full_button.lower()
        if 'facebook' in low: label = "Facebook"
        elif 'instagram' in low: label = "Instagram"
        elif 'twitter' in low: label = "Twitter"
        elif 'youtube' in low: label = "YouTube"
        elif 'trash' in low or 'delete' in low: label = "Delete"
        elif 'edit' in low or 'pen' in low: label = "Edit"
        new_start = start_tag.replace('<a', f'<a aria-label="{label}"')
        return full_button.replace(start_tag, new_start)
    content = a_pattern.sub(a_replacer, content)
    
    # Extra fix for <a>&times;</a> or <button>&times;</button>
    content = re.sub(r'(<(?:a|button)[^>]*>)\s*(?:&times;|✕)\s*</(?:a|button)>', 
                     lambda m: m.group(0) if 'aria-label' in m.group(1) else m.group(0).replace(m.group(1), m.group(1).replace('<' + m.group(1)[1:2].split()[0], '<' + m.group(1)[1:2].split()[0] + ' aria-label="Close"')), 
                     content, flags=re.IGNORECASE)
    return content

def process_admin_confirms(content):
    # Convert onclick="return confirm('Are you sure?')" to data-action="confirm" data-confirm-msg="..."
    def replacer(match):
        msg = match.group(1).replace('"', '&quot;')
        return f'data-action="confirm" data-confirm="{msg}"'
        
    content = re.sub(r'onclick\s*=\s*["\']return confirm\(([\'"])(.*?)\1\);?["\']', replacer, content, flags=re.IGNORECASE)
    content = re.sub(r'onsubmit\s*=\s*["\']return confirm\(([\'"])(.*?)\1\);?["\']', replacer, content, flags=re.IGNORECASE)
    return content

def strip_inline_events(content):
    # Catch any remaining inline events. We skip if it contains PHP just to be safe, but actually admin panel might have PHP in them.
    # Wait, if we replace onclick with data-action, we should have caught them. We'll aggressively strip the rest.
    return re.sub(r'\bon(click|focus|blur|change|submit)\s*=\s*["\'][^"\']*["\']', '', content, flags=re.IGNORECASE)

print("Processing Includes (Batch 2)...")
for filepath in include_files:
    with open(filepath, 'r', encoding='utf-8') as f: content = f.read()
    orig = content
    content = process_csrf(content, is_admin=False)
    content = process_labels(content)
    content = process_aria_labels(content)
    content = strip_inline_events(content)
    if content != orig:
        with open(filepath, 'w', encoding='utf-8') as f: f.write(content)
        print(f"  Processed {os.path.basename(filepath)}")

print("\nProcessing Admin (Batch 3)...")
for filepath in admin_files:
    with open(filepath, 'r', encoding='utf-8') as f: content = f.read()
    orig = content
    content = process_csrf(content, is_admin=True)
    content = process_labels(content)
    content = process_aria_labels(content)
    content = process_admin_confirms(content)
    content = strip_inline_events(content)
    if content != orig:
        with open(filepath, 'w', encoding='utf-8') as f: f.write(content)
        print(f"  Processed {os.path.basename(filepath)}")

print("Done Phase 2 Batch 2 & 3.")
