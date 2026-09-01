import re

fpath_detail = r'g:\Antigravity\leisure_loop_site\public\package-detail.php'
fpath_static = r'g:\Antigravity\leisure_loop_site\public\package.php'

with open(fpath_detail, 'r', encoding='utf-8') as f:
    detail_html = f.read()

with open(fpath_static, 'r', encoding='utf-8') as f:
    static_html = f.read()

def get_block(text, start_str, end_str):
    start = text.find(start_str)
    if start == -1: return ""
    end = text.find(end_str, start)
    if end == -1: return ""
    return text[start:end+len(end_str)]

# 1. Grab the correct Private Concierge from static
static_form = get_block(static_html, '<!-- Right: Sticky Sidebar -->', '</aside>')

# Now we need to inject static_form before </main> in detail_html
# Wait, let's just replace the entire Overview section in detail_html with the 2x2 grid version
# And inject the static_form right before </main>

new_overview = """<!-- Overview -->
<section>
<p class="font-label-caps text-secondary tracking-[0.3em] mb-4">THE CURATION</p>
<h2 class="font-display-lg italic text-secondary mb-8">Overview</h2>
<?php if (!empty($description_rich)): ?>
<div class="text-on-surface-variant leading-relaxed text-lg mb-8">
    <?php echo nl2br(htmlspecialchars($description_rich)); ?>
</div>
<?php endif; ?>

<?php if (!empty($highlights_arr)): ?>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-on-surface-variant leading-relaxed">
    <?php foreach ($highlights_arr as $hl): ?>
    <div class="flex gap-4">
        <span class="material-symbols-outlined text-secondary text-sm mt-1">verified</span>
        <p><?php echo htmlspecialchars($hl); ?></p>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>
</section>"""

# Replace the old overview in detail_html
old_overview_pattern = re.compile(r'<!-- Overview -->.*?</section>', re.DOTALL)
detail_html = old_overview_pattern.sub(new_overview, detail_html)

# Inject static_form
# Find </main>
if static_form:
    if "<!-- Right: Sticky Sidebar -->" not in detail_html:
        detail_html = detail_html.replace('</main>', f'{static_form}\n</main>')

with open('g:/Antigravity/leisure_loop_site/public/package-detail.php', 'w', encoding='utf-8') as f:
    f.write(detail_html)

print("Fixed Layout applied!")
