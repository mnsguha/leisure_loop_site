import os
import re

PUBLIC_DIR = r'g:\Antigravity\leisure_loop_site\public'
CSS_DIR = os.path.join(PUBLIC_DIR, 'css')
JS_DIR = os.path.join(PUBLIC_DIR, 'js', 'modules')

os.makedirs(CSS_DIR, exist_ok=True)
os.makedirs(JS_DIR, exist_ok=True)

bundles = {
    'home': ['index.php', 'custom-typo-showcase.html', 'sikkim-static-backup.php'],
    'packages': ['all-tours.php', 'packages.php', 'package.php', 'package-detail.php', 'pkg.php'],
    'cabs': ['cabs.php', 'cab-detail.php'],
    'destinations': ['destinations.php', 'destination-details.php', 'himalaya.php'],
    'hotels': ['hotels.php', 'hotel-detail.php'],
    'content-pages': ['blog.php', 'blog-detail.php', 'events.php', 'faq.php', 'gallery.php', 'b2b.php']
}

style_pattern = re.compile(r'<style[^>]*>(.*?)</style>', re.IGNORECASE | re.DOTALL)
script_pattern = re.compile(r'<script(?![^>]*\bsrc\s*=)[^>]*>(.*?)</script>', re.IGNORECASE | re.DOTALL)
event_pattern = re.compile(r'\bon(click|change|submit|focus|blur|load)\s*=\s*"([^"]*)"', re.IGNORECASE)

delegation_template = """'use strict';

document.addEventListener('DOMContentLoaded', () => {{

    // Extracted Scripts
{scripts}

    // Generic Event Delegation
    document.addEventListener('click', (e) => {{
        const actionEl = e.target.closest('[data-action], [data-href]');
        if (actionEl && actionEl.hasAttribute('data-href')) {{
            window.location.href = actionEl.getAttribute('data-href');
            return;
        }}
        
        if (!actionEl) return;
        const action = actionEl.getAttribute('data-action');
        
        // Modal toggling
        if (action === 'open-modal') {{
            const target = actionEl.getAttribute('data-target');
            if (target) {{
                const modal = document.querySelector(target) || document.getElementById(target);
                if (modal) modal.classList.add('is-active');
            }} else {{
                document.querySelectorAll('.modal').forEach(m => m.classList.add('is-active'));
            }}
        }} else if (action === 'close-modal') {{
            const target = actionEl.getAttribute('data-target');
            if (target) {{
                const modal = document.querySelector(target) || document.getElementById(target);
                if (modal) modal.classList.remove('is-active');
            }} else {{
                document.querySelectorAll('.modal.is-active, .modal.active').forEach(m => m.classList.remove('is-active', 'active'));
            }}
        }} else if (action === 'toggle-tab') {{
            const targetId = actionEl.getAttribute('data-target');
            // Remove active from all tabs in same group
            const group = actionEl.getAttribute('data-group') || 'default';
            document.querySelectorAll(`[data-action="toggle-tab"][data-group="${{group}}"]`).forEach(el => el.classList.remove('is-active', 'active'));
            document.querySelectorAll(`.tab-content[data-group="${{group}}"]`).forEach(el => el.classList.remove('is-active', 'active'));
            
            actionEl.classList.add('is-active');
            const targetContent = document.getElementById(targetId);
            if(targetContent) targetContent.classList.add('is-active');
        }} else if (action === 'toggle-accordion') {{
            actionEl.classList.toggle('is-active');
            const content = actionEl.nextElementSibling;
            if(content) content.classList.toggle('is-active');
        }} else if (action.startsWith('eval:')) {{
            // Extremely generic fallback - to be manually replaced if found
            console.warn('Unhandled inline action:', action);
        }}
    }});
    
    // Intercept Lead Forms
    const leadForms = document.querySelectorAll('form[action*="/api/submit-lead"], form[action*="/api/v1/leads"], form.lead-form');
    leadForms.forEach(form => {{
        form.addEventListener('submit', (e) => {{
            // Let the JS handle it or let standard submit happen if we don't preventDefault.
            // Requirement: "prepped to target POST /api/v1/leads without inline execution"
            // We just ensure action is correct.
        }});
    }});
}});
"""

all_events = []

for bundle, file_list in bundles.items():
    css_content = [".is-active { display: block !important; }\n.is-active-flex { display: flex !important; }\n"]
    js_raw_scripts = []
    
    for filename in file_list:
        filepath = os.path.join(PUBLIC_DIR, filename)
        if not os.path.exists(filepath):
            print(f"File not found: {filepath}")
            continue
            
        with open(filepath, 'r', encoding='utf-8') as f:
            content = f.read()

        # 1. Extract and replace Styles
        styles = style_pattern.findall(content)
        if styles:
            for s in styles:
                css_content.append(f'/* Extracted from {filename} */\n{s.strip()}\n')
            content = style_pattern.sub(rf'<link rel="stylesheet" href="/css/{bundle}.css">', content, count=1)
            content = style_pattern.sub('', content)

        # 2. Extract and replace Scripts
        scripts = script_pattern.findall(content)
        scripts = [s for s in scripts if s.strip()]
        if scripts:
            for s in scripts:
                js_raw_scripts.append(f'/* Extracted from {filename} */\n{s.strip()}\n')
            content = script_pattern.sub(rf'<script src="/js/modules/{bundle}.js" defer></script>', content, count=1)
            content = script_pattern.sub('', content)

        # 3. Patch Forms Action
        # "prepped to target POST /api/v1/leads"
        # We will replace action="api/submit-lead.php" or similar to "/api/v1/leads"
        content = re.sub(r'action\s*=\s*["\'](?:api/)?submit-lead\.php["\']', 'action="/api/v1/leads" method="POST"', content, flags=re.IGNORECASE)
        # Add action to forms missing it if they are clearly lead forms
        # We'll just rely on the regex replacing known endpoints.

        # 4. Patch events temporarily by capturing them
        # We will replace them with data-action="eval:..." to ensure no inline JS remains, 
        # and then we can grep for "eval:" to see what we missed.
        
        def event_replacer(match):
            evt_type = match.group(1).lower()
            code = match.group(2)
            all_events.append((filename, evt_type, code))
            
            # Simple common replacements
            if code == 'openModal()' or code.startswith('openModal('):
                return 'data-action="open-modal"'
            elif code == 'closeModal()' or code.startswith('closeModal('):
                return 'data-action="close-modal"'
            elif code.startswith("window.location.href="):
                url = re.search(r"window\.location\.href=['\"]([^'\"]*)['\"]", code)
                if url:
                    return f'data-href="{url.group(1)}"'
            
            if evt_type == 'click':
                return f'data-action="eval:{code}"'
            elif evt_type == 'change':
                return f'data-change="eval:{code}"'
            elif evt_type == 'submit':
                return f'data-submit="eval:{code}"'
            else:
                return f'data-{evt_type}="eval:{code}"'

        content = event_pattern.sub(event_replacer, content)

        with open(filepath, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Processed {filename} -> {bundle}")
        
    # Write Bundle CSS
    with open(os.path.join(CSS_DIR, f"{bundle}.css"), 'w', encoding='utf-8') as f:
        f.write('\n'.join(css_content))
        
    # Write Bundle JS
    bundle_js_content = delegation_template.format(scripts='\n'.join(js_raw_scripts))
    # Remove tailwind config blocks to avoid pollution
    bundle_js_content = re.sub(r'tailwind\.config\s*=\s*\{.*?\n\s*\}\s*\n', '', bundle_js_content, flags=re.DOTALL)
    
    with open(os.path.join(JS_DIR, f"{bundle}.js"), 'w', encoding='utf-8') as f:
        f.write(bundle_js_content)

# Dump events for manual review
with open(r'g:\Antigravity\leisure_loop_site\scratch\events_batch3.txt', 'w', encoding='utf-8') as f:
    for e in all_events:
        f.write(f"{e[0]} | {e[1]} | {e[2]}\n")

print("Batch 3 extraction complete. Events logged to events_batch3.txt")
