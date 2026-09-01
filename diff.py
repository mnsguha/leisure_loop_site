import difflib

with open(r"G:\Antigravity\Backup\leisure_loop_site_backup_20260522\public\index.php", "r", encoding="utf-8") as f1:
    lines1 = f1.readlines()

with open(r"g:\Antigravity\leisure_loop_site\public\index.php", "r", encoding="utf-8") as f2:
    lines2 = f2.readlines()

diff = difflib.unified_diff(lines1, lines2, fromfile='backup', tofile='current', n=3)
with open("diff_out.txt", "w", encoding="utf-8") as out:
    for line in diff:
        out.write(line)
