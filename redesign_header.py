import re

file_path = 'includes/mobile_home.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

start_marker = "<!-- Mobile Header -->"
end_marker = "<!-- 1. Hero Section -->"

start_idx = content.find(start_marker)
end_idx = content.find(end_marker)

if start_idx != -1 and end_idx != -1:
    new_section = """<!-- Mobile Header -->
<header id="mobileHeader" class="fixed top-0 w-full z-50 px-4 h-16 flex items-center justify-between transition-all duration-300 bg-transparent border-b border-transparent">
    <div class="flex items-center gap-2">
        <a href="index.php" class="flex items-center">
            <img src="assets/img/logo.png" alt="Leisure Loop Trip" style="height: 36px; width: auto; max-width: 150px; object-fit: contain; object-position: left; filter: drop-shadow(0px 2px 4px rgba(0,0,0,0.8));" onerror="this.outerHTML='<h1 class=\'font-headline-md text-xl font-bold tracking-tight text-white\'>Leisure Loop</h1>'">
        </a>
    </div>
    <a href="contact.php" class="text-black px-4 py-1.5 rounded-full font-bold text-xs shadow-[0_4px_15px_rgba(197,160,89,0.4)] transition-transform active:scale-95 flex items-center gap-1.5" style="background: linear-gradient(135deg, #e6c888, #b88a44);">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
        Contact
    </a>
</header>
<script>
    document.addEventListener('scroll', function() {
        const header = document.getElementById('mobileHeader');
        if (window.scrollY > 50) {
            header.classList.remove('bg-transparent', 'border-transparent');
            header.classList.add('bg-[#0a0a0a]/80', 'backdrop-blur-xl', 'border-[rgba(255,255,255,0.1)]');
        } else {
            header.classList.add('bg-transparent', 'border-transparent');
            header.classList.remove('bg-[#0a0a0a]/80', 'backdrop-blur-xl', 'border-[rgba(255,255,255,0.1)]');
        }
    });
</script>

<main class="">
"""
    
    new_content = content[:start_idx] + new_section + content[end_idx:]
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(new_content)
    print("Redesigned Header successfully.")
else:
    print("Could not find markers.")
