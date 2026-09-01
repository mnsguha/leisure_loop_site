import sys, io, re
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

import glob
for f in glob.glob('G:/Antigravity/leisure_loop_site/scratch/*'):
    print(f)
