import sys
import re

def main():
    try:
        with open(r'G:\Antigravity\leisure_loop_site\scratch\original_style.css', 'r', encoding='utf-8') as f:
            content = f.read()
    except Exception as e:
        print(f"Error reading: {e}")
        return

    # 1. Global replacements
    content = content.replace("font-family: 'Playfair Display', serif;", "font-family: var(--font-title);")
    content = content.replace("font-family: 'Inter', sans-serif;", "font-family: var(--font-body);")

    # 2. Specific heading replacements
    # Using regex to target specific classes safely
    patterns = {
        r'(\.section-title\s*\{[^}]+?)': r'\1\n    font-family: var(--font-heading);',
        r'(\.sidebar-title\s*\{[^}]+?)font-family:\s*var\(--font-title\);': r'\1font-family: var(--font-heading);',
        r'(\.catalog-card-title\s*\{[^}]+?)font-family:\s*var\(--font-title\);': r'\1font-family: var(--font-heading);',
        r'(\.accreditations-title\s*\{[^}]+?)font-family:\s*var\(--font-title\);': r'\1font-family: var(--font-heading);',
        r'(\.footer-heading\s*\{[^}]+?)font-family:\s*var\(--font-body\);': r'\1font-family: var(--font-heading);'
    }

    for pat, repl in patterns.items():
        content = re.sub(pat, repl, content, count=1, flags=re.DOTALL)

    try:
        with open(r'G:\Antigravity\leisure_loop_site\public\css\style.css', 'w', encoding='utf-8') as f:
            f.write(content)
        print("Successfully updated style.css")
    except Exception as e:
        print(f"Error writing: {e}")

if __name__ == '__main__':
    main()
