import sys, io, glob
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')

for f in glob.glob('G:/Antigravity/leisure_loop_site/scratch/*'):
    try:
        with open(f, 'r', encoding='utf-8') as file:
            content = file.read()
            if 'leisure-difference-bg' in content:
                print(f'Found in {f}')
                idx = content.find('leisure-difference-bg')
                print(content[max(0, idx-50):idx+300])
                print('---')
    except:
        pass
