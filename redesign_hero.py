import re

file_path = 'includes/mobile_home.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

start_marker = "<!-- 1. Hero Section -->"
end_marker = "<!-- Stats Strip -->"

start_idx = content.find(start_marker)
end_idx = content.find(end_marker)

if start_idx != -1 and end_idx != -1:
    new_section = """<!-- 1. Hero Section -->
<section class="relative h-[85vh] min-h-[550px] flex flex-col justify-end px-4 pb-12">
    <div class="absolute inset-0 z-0">
        <?php if (!empty($hero_slides) && $hero_slides[0]['type'] === 'video'): ?>
            <video class="w-full h-full object-cover" autoplay muted loop playsinline>
                <source src="<?php echo htmlspecialchars($mobile_hero_bg); ?>" type="video/mp4">
            </video>
        <?php else: ?>
            <img class="w-full h-full object-cover" src="<?php echo htmlspecialchars($mobile_hero_bg); ?>" alt="Hero">
        <?php endif; ?>
        <div class="absolute inset-0 bg-gradient-to-t from-black via-black/40 to-transparent"></div>
    </div>
    <div class="relative z-10 space-y-6">
        <h2 class="text-white font-headline-lg-mobile text-3xl font-serif leading-snug drop-shadow-lg text-center px-2">
            <?php echo $settings['hero_text_main'] ?? 'Your Journey,<br/>Perfectly Planned'; ?>
        </h2>
        <div class="backdrop-blur-xl bg-black/40 border border-white/10 p-5 rounded-[24px] shadow-2xl space-y-4">
            <form action="api-submit-lead.php" method="POST" class="space-y-3">
                <div class="flex flex-col gap-3">
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                        </div>
                        <input class="w-full pl-12 pr-4 py-3.5 bg-black/50 border border-white/10 rounded-2xl focus:ring-1 focus:ring-[var(--gold)] focus:border-[var(--gold)] text-white text-[15px] placeholder-gray-400 outline-none transition-all" placeholder="Where to?" type="text" name="destination" required/>
                    </div>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="var(--gold)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                        </div>
                        <input class="w-full pl-12 pr-4 py-3.5 bg-black/50 border border-white/10 rounded-2xl focus:ring-1 focus:ring-[var(--gold)] focus:border-[var(--gold)] text-white text-[15px] placeholder-gray-400 outline-none transition-all" placeholder="When?" type="date" name="date" required/>
                    </div>
                </div>
                <button type="submit" class="w-full text-black py-4 rounded-2xl font-bold text-[15px] shadow-[0_4px_15px_rgba(197,160,89,0.3)] active:scale-[0.98] transition-transform flex justify-center items-center gap-2 mt-2" style="background: linear-gradient(135deg, #e6c888, #b88a44);">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    Search Trips
                </button>
            </form>
        </div>
    </div>
</section>

"""
    
    new_content = content[:start_idx] + new_section + content[end_idx:]
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(new_content)
    print("Redesigned Hero Section successfully.")
else:
    print("Could not find markers.")
