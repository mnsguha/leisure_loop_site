import sys

def update_views_final():
    path = "G:/Antigravity/travel_voucher_app/vouchers/views.py"
    with open(path, 'r') as f:
        content = f.read()
    
    # Correct the redirect name
    content = content.replace('return redirect("settings")', 'return redirect("system-settings")')
    
    with open(path, 'w') as f:
        f.write(content)
    print("Successfully corrected views.py")

if __name__ == "__main__":
    update_views_final()
