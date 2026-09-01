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
    return text[start:end]

# Extracted blocks from package-detail.php
trust_badges = get_block(detail_html, "<!-- ✦ Trust Badges Strip ✦ -->", "<!-- ✦ Tour Highlights ✦ -->")
itinerary = get_block(detail_html, "<!-- ✦ Interactive Accordion Itinerary ✦ -->", "<!-- ✦ Animated Journey Map ✦ -->")
map_block = get_block(detail_html, "<!-- ✦ Animated Journey Map ✦ -->", "<!-- Accordion + Map JS -->")
map_js = get_block(detail_html, "<!-- Accordion + Map JS -->", "<!-- Exquisite Inclusions / Exclusions -->")
terms = get_block(detail_html, "<!-- ✦ Terms & Conditions ✦ -->", "<!-- ✦ Dynamic Advertisement Banner ✦ -->")
other_tours = get_block(detail_html, "<!-- ✦ Related Packages Section (\"Maybe you like\") ✦ -->", "<!-- Lead Capture Modal Overlay -->")

# Extract the PHP logic and CSS from package-detail.php
php_top = get_block(detail_html, "<?php", "<!-- Custom Premium Fonts and Leaflet map styling -->")

css_block = get_block(detail_html, "<!-- Custom Premium Fonts and Leaflet map styling -->", "<!-- JSON-LD Product Schema for SEO -->")

# Replace hero with static hero
# The static hero starts with `<section class="relative h-[870px]`
static_hero = get_block(static_html, '<!-- Hero Portal -->', '<!-- Details Ribbon -->')
# We need to make it dynamic
static_hero = static_hero.replace(
    "style=\"background-image: url('https://lh3.googleusercontent.com/aida-public/AB6AXuDLQz9GpfKYIVGmWL6fy3-AOv6QlNrIlU3DX-fVqx-bZTsUvLKd0Y4u8PWNLXxeJIa8X9Bg7mWji1kZPXdp01mofAwQ226fEcUQel8Kn28NVHiNkbrGhaBFVaSXSBHmJalomdRcm0XN8ixjASuCYZrgKmV6iaibK2epnT-5zx0BtLAjiSI36XJzGp1iIf2-BvnSQMnsl1XI-cqimGBy1KlsOiHevcbQCgQOy2MEufZUXd_z1WmfKSocupsLSlr2ZbMgTdWuTlauuRzc')\"",
    "style=\"background-image: url('<?php echo htmlspecialchars(!empty($pkg['banner_image_url']) ? $pkg['banner_image_url'] : $pkg['image_url']); ?>')\""
)
static_hero = static_hero.replace(
    "North Sikkim Frozen Lake Tour",
    "<?php echo htmlspecialchars($pkg['title']); ?>"
)
static_hero = static_hero.replace(
    "05 Nights / 06 Days",
    "<?php echo str_pad($pkg['nights'], 2, '0', STR_PAD_LEFT); ?> Nights / <?php echo str_pad($pkg['days'], 2, '0', STR_PAD_LEFT); ?> Days"
)
static_hero = static_hero.replace(
    "Sikkim, India",
    "<?php echo htmlspecialchars($pkg['destination']); ?>"
)
static_hero = static_hero.replace(
    "4.8",
    "<?php echo number_format($rating_score, 1); ?>"
)

static_ribbon = get_block(static_html, '<!-- Details Ribbon -->', '<!-- Main Content Grid -->')
static_ribbon = static_ribbon.replace("VD-0001", "<?php echo $tour_code; ?>")
static_ribbon = static_ribbon.replace("Sikkim</p>", "<?php echo htmlspecialchars($pkg['destination']); ?></p>")
static_ribbon = static_ribbon.replace("Honeymoon, Alpine Lake", "<?php echo $tour_type; ?>")
static_ribbon = static_ribbon.replace(
    "From ₹12,999 <span class=\"text-xs text-on-surface-variant line-through ml-1\">₹18,999</span>",
    "From ₹<?php echo number_format($pkg['price']); ?> <?php if($original_price): ?><span class=\"text-xs text-on-surface-variant line-through ml-1\">₹<?php echo number_format($original_price); ?></span><?php endif; ?>"
)

static_overview = get_block(static_html, '<!-- Overview -->', '<!-- Visual Journal -->')
# We replace the Overview content with the dynamic one
# Actually, let's keep the layout but put PHP inside
overview_php = """
<!-- Overview -->
<section>
<p class="font-label-caps text-secondary tracking-[0.3em] mb-4">THE CURATION</p>
<h2 class="font-display-lg italic text-secondary mb-8">Overview</h2>
<?php
    $overview_text = '';
    if (!empty($description_rich)) {
        $overview_text = $description_rich;
    } elseif (!empty($highlights_arr)) {
        $overview_text = implode("\\n", $highlights_arr);
    }
?>
<?php if (!empty($overview_text)): ?>
<div class="text-on-surface-variant leading-relaxed text-lg">
    <?php echo nl2br(htmlspecialchars($overview_text)); ?>
</div>
<?php endif; ?>
</section>
"""

# Visual Journal
visual_php = """
<!-- Visual Journal -->
<?php if (!empty($photos_arr)): ?>
<section>
<h2 class="font-display-md text-white mb-10 mt-10">Visual Journal</h2>
<div class="grid grid-cols-2 gap-4 h-[600px]">
    <?php if (isset($photos_arr[0])): ?>
    <div class="relative overflow-hidden rounded-2xl group mosaic-container h-full">
        <img class="w-full h-full object-cover mosaic-img" src="<?php echo htmlspecialchars($photos_arr[0]); ?>" />
        <div class="absolute inset-0 bg-gradient-to-t from-background/90 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-8">
            <p class="font-accent-serif italic text-secondary text-xl">Immersive Landscapes</p>
        </div>
    </div>
    <?php endif; ?>
    <div class="flex flex-col gap-4 h-full">
        <?php if (isset($photos_arr[1])): ?>
        <div class="relative flex-1 overflow-hidden rounded-2xl group mosaic-container">
            <img class="w-full h-full object-cover mosaic-img" src="<?php echo htmlspecialchars($photos_arr[1]); ?>" />
            <div class="absolute inset-0 bg-gradient-to-t from-background/90 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
                <p class="font-accent-serif italic text-secondary text-lg">Luxury Escapes</p>
            </div>
        </div>
        <?php endif; ?>
        <?php if (isset($photos_arr[2])): ?>
        <div class="relative flex-1 overflow-hidden rounded-2xl group mosaic-container">
            <img class="w-full h-full object-cover mosaic-img" src="<?php echo htmlspecialchars($photos_arr[2]); ?>" />
            <div class="absolute inset-0 bg-gradient-to-t from-background/90 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-6">
                <p class="font-accent-serif italic text-secondary text-lg">Curated Memories</p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>
</section>
<?php endif; ?>
"""

# Inclusions / Exclusions
inc_exc_php = """
<!-- Inclusions/Exclusions -->
<section class="grid grid-cols-1 md:grid-cols-2 gap-8 mt-12">
    <div class="bg-inclusion-green border border-green-500/20 rounded-3xl p-8">
        <h3 class="text-green-400 font-bold text-xl mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined">check_circle</span>
            Inclusions
        </h3>
        <ul class="space-y-4 text-on-surface-variant">
            <?php foreach ($inclusions_arr as $inc): ?>
            <li class="flex items-start gap-3">
                <span class="material-symbols-outlined text-green-500 text-sm mt-1">check</span>
                <?php echo htmlspecialchars($inc); ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="bg-exclusion-red border border-red-500/20 rounded-3xl p-8">
        <h3 class="text-red-400 font-bold text-xl mb-6 flex items-center gap-2">
            <span class="material-symbols-outlined">cancel</span>
            Exclusions
        </h3>
        <ul class="space-y-4 text-on-surface-variant">
            <?php foreach ($exclusions_arr as $exc): ?>
            <li class="flex items-start gap-3">
                <span class="material-symbols-outlined text-red-500 text-sm mt-1">close</span>
                <?php echo htmlspecialchars($exc); ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
"""

# Right column form
static_form = get_block(static_html, '<!-- Right: Private Concierge Form -->', '</main>')

# Compile everything together
# Include tailwind from static
tailwind_head = get_block(static_html, '<script src="https://cdn.tailwindcss.com', '<div class="dark')

final_html = f"""{php_top}
{css_block}
{tailwind_head}
<div class="dark bg-background text-on-background font-body-md selection:bg-secondary selection:text-on-secondary">
{static_hero}
{static_ribbon}
<main class="max-w-container-max mx-auto px-gutter py-20 flex flex-col lg:flex-row gap-12">
    <!-- Left: Narrative -->
    <div class="flex-1 space-y-24">
        {overview_php}
        {trust_badges}
        {visual_php}
        {itinerary}
        {inc_exc_php}
        {map_block}
        {terms}
    </div>
    {static_form}
</main>
{other_tours}
{map_js}
</div>
<?php include '../includes/footer.php'; ?>
"""

with open('g:/Antigravity/leisure_loop_site/public/package-detail-new.php', 'w', encoding='utf-8') as f:
    f.write(final_html)

print("Created package-detail-new.php!")
