import glob

files = glob.glob('public/admin/*-form.php') + ['public/admin/hotel-gallery.php', 'public/admin/hotel-inventory.php']
with open('headers.txt', 'w', encoding='utf-8') as out_file:
    for f in files:
        with open(f, 'r', encoding='utf-8') as file:
            content = file.read()
        start = content.find('<div class="header"')
        if start != -1:
            end = content.find('</div>', start)
            end = content.find('</div>', end + 1)
            end = content.find('</div>', end + 1)
            out_file.write(f'--- {f} ---\n')
            out_file.write(content[start:end+6] + '\n\n')
