import difflib
import sys

def main():
    try:
        with open(r'G:\Antigravity\leisure_loop_site\scratch\original_style.css', 'r', encoding='utf-8') as f:
            original = f.readlines()
    except Exception as e:
        print(f"Error reading original: {e}")
        return

    try:
        with open(r'G:\Antigravity\leisure_loop_site\public\css\style.css', 'r', encoding='utf-8') as f:
            broken = f.readlines()
    except Exception as e:
        print(f"Error reading broken: {e}")
        return

    diff = list(difflib.unified_diff(original, broken, fromfile='original', tofile='broken', n=1))
    
    with open(r'G:\Antigravity\leisure_loop_site\scratch\diff_report.txt', 'w', encoding='utf-8') as f:
        f.writelines(diff)
    print("Diff report generated successfully.")

if __name__ == '__main__':
    main()
