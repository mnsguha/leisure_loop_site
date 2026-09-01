import sys

def update_urls():
    path = "G:/Antigravity/travel_voucher_app/vouchers/urls.py"
    with open(path, 'r') as f:
        lines = f.readlines()
    
    new_lines = []
    for line in lines:
        if "TestAIConnectionApiView," in line:
            new_lines.append(line)
            new_lines.append("    TestWebsiteSyncApiView,\n")
        else:
            new_lines.append(line)
            
    with open(path, 'w') as f:
        f.writelines(new_lines)
    print("Successfully updated urls.py")

if __name__ == "__main__":
    update_urls()
