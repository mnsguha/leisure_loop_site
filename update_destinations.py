import re
import os

filepath = 'public/destinations.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add GSAP to head
head_inject = """    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/gsap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.2/ScrollTrigger.min.js"></script>
</head>"""
content = content.replace("</head>", head_inject)

# 2. Replace the Hero Section
# We find the Hero Section block between "<!-- Hero Section -->" and "<!-- The Narrative (Editorial Section) -->"
hero_start = content.find("<!-- Hero Section -->")
narrative_start = content.find("<!-- The Narrative (Editorial Section) -->")

hero_replacement = """<!-- Hero Section -->
    <section class="parable-hero" style="position: relative; width: 100%; height: 110vh; overflow: hidden; background: #e0f0ff; z-index: 1;">
        <!-- Cloud Reveal Animation overlay -->
        <div class="cloud-reveal-container" style="position:absolute; inset:0; z-index:99; pointer-events:none; display:flex;">
            <div class="cloud-door cloud-door-left" style="width:50%; height:100%; background:black; transform-origin:left;"></div>
            <div class="cloud-door cloud-door-right" style="width:50%; height:100%; background:black; transform-origin:right;"></div>
        </div>

        <!-- Stacked Layers for ParableVC Mouse-Move & Scroll Parallax -->
        <img src="images/parallax/sunset_sky.png" class="p-layer p-layer-sky" data-depth="0.05" alt="Sky" style="position: absolute; inset: -10%; width: 120%; height: 120%; object-fit: cover; z-index: 1; pointer-events: none;">
        
        <!-- Canvas for animated valley clouds -->
        <canvas id="valleyClouds" class="p-layer" data-depth="0.10" style="position: absolute; inset: -10%; width: 120%; height: 120%; z-index: 1; pointer-events: none; mix-blend-mode: screen; opacity: 0.8;"></canvas>

        <img src="images/parallax/sunset_mountains.png" class="p-layer p-layer-back" data-depth="0.15" alt="Mountains" style="position: absolute; left: -5%; bottom: -10%; width: 110%; height: 120%; object-fit: cover; z-index: 2; pointer-events: none;">

        <!-- Typography sandwiched between layers -->
        <div class="p-layer p-layer-text relative z-30 px-margin-mobile md:px-margin-desktop text-center flex flex-col justify-center items-center h-full" data-depth="0.2" style="position: absolute; top: 0; left: 0; width: 100%; z-index: 3; pointer-events: none;">
            <div style="transform: translateY(-20vh);">
                <nav class="mb-8 font-label-caps text-label-caps text-secondary uppercase tracking-widest drop-shadow-md">
                    Home &gt; Destinations &gt; <span class="text-white">Sikkim</span>
                </nav>
                <h1 class="font-hero-display text-hero-display-mobile md:text-hero-display text-secondary mb-4 drop-shadow-[0_10px_30px_rgba(0,0,0,0.8)]">
                    SIKKIM
                </h1>
                <p class="font-headline-accent text-headline-accent text-white tracking-[0.4em] uppercase italic drop-shadow-md">
                    Himalayan Majesty &amp; <span class="text-secondary italic">Elite Travel Experiences</span>
                </p>
            </div>
        </div>

        <img src="images/parallax/sunset_village.png" class="p-layer p-layer-mid" data-depth="0.4" alt="Village" style="position: absolute; left: -5%; bottom: -15%; width: 110%; height: 125%; object-fit: cover; z-index: 4; pointer-events: none; transform-origin: center bottom;">
        
        <!-- 3D Roaming Clouds -->
        <div class="p-layer" data-depth="0.5" style="position: absolute; inset: -10%; width: 120%; height: 120%; z-index: 5; pointer-events: none;">
            <img src="images/parallax/custom_cloud_1.png" alt="Cloud" class="cloud-img-1" style="position:absolute; top:40%; left:10%; opacity:0.6; filter:blur(2px);">
        </div>
        <div class="p-layer" data-depth="0.7" style="position: absolute; inset: -10%; width: 120%; height: 120%; z-index: 6; pointer-events: none;">
            <img src="images/parallax/custom_cloud_2.png" alt="Cloud" class="cloud-img-2" style="position:absolute; top:60%; right:15%; opacity:0.8; filter:blur(4px);">
        </div>
        <div class="p-layer" data-depth="0.6" style="position: absolute; inset: -10%; width: 120%; height: 120%; z-index: 5; pointer-events: none;">
            <img src="images/parallax/custom_cloud_3.png" alt="Cloud" class="cloud-img-3" style="position:absolute; top:20%; left:50%; opacity:0.5; filter:blur(1px);">
        </div>

        <img src="images/parallax/sunset_flags.png" class="p-layer p-layer-foremost" data-depth="0.9" alt="Prayer Flags" style="position: absolute; left: -10%; bottom: -30%; width: 120%; height: 150%; object-fit: cover; z-index: 7; pointer-events: none; transform-origin: center bottom;">
        
        <!-- Scroll Indicator -->
        <div class="absolute bottom-24 left-1/2 -translate-x-1/2 z-40 flex flex-col items-center gap-3 p-layer p-layer-indicator" data-depth="0">
            <span class="font-label-caps text-[10px] tracking-[0.3em] text-secondary opacity-80 drop-shadow-md">SCROLL TO EXPLORE</span>
            <div class="w-[2px] h-12 bg-gradient-to-b from-secondary to-transparent relative">
                <div class="absolute top-0 left-1/2 -translate-x-1/2 w-1.5 h-1.5 bg-secondary rounded-full pulse-dot"></div>
            </div>
        </div>

        <!-- Quick Facts Bar -->
        <div class="absolute bottom-0 w-full flex justify-center z-40 mb-12 p-layer p-layer-facts" data-depth="0.05">
            <div class="w-full max-w-6xl px-margin-mobile">
                <div class="glass-card rounded-2xl p-6 flex flex-wrap justify-between items-center gap-8 md:gap-4 shadow-[0_30px_60px_rgba(0,0,0,0.5)] border border-secondary/20 bg-[#0a0a0a]/80 backdrop-blur-xl">
                    <div class="flex flex-1 items-center justify-center gap-3 border-r border-secondary/10 px-4 last:border-0">
                        <span class="material-symbols-outlined text-secondary">mountain_flag</span>
                        <div class="text-left">
                            <span class="block font-label-caps text-[10px] text-on-surface-variant uppercase tracking-widest">Altitude</span>
                            <span class="font-body-md text-white font-semibold">3500m+</span>
                        </div>
                    </div>
                    <div class="flex flex-1 items-center justify-center gap-3 border-r border-secondary/10 px-4 last:border-0">
                        <span class="material-symbols-outlined text-secondary">calendar_month</span>
                        <div class="text-left">
                            <span class="block font-label-caps text-[10px] text-on-surface-variant uppercase tracking-widest">Best Time</span>
                            <span class="font-body-md text-white font-semibold">Oct-May</span>
                        </div>
                    </div>
                    <div class="flex flex-1 items-center justify-center gap-3 px-4 last:border-0">
                        <span class="material-symbols-outlined text-secondary">schedule</span>
                        <div class="text-left">
                            <span class="block font-label-caps text-[10px] text-on-surface-variant uppercase tracking-widest">Duration</span>
                            <span class="font-body-md text-white font-semibold">7-10 Days</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Spacer to allow parallax scrolling before the next section overlaps -->
    <div class="parable-spacer" style="height: 150vh; width: 100%; position: relative; z-index: 0; pointer-events: none;"></div>

"""
content = content[:hero_start] + hero_replacement + content[narrative_start:]

# 3. Inject JS Logic
js_inject = """
    function initValleyClouds() {
        const canvas = document.getElementById('valleyClouds');
        if (!canvas) return;
        const ctx = canvas.getContext('2d');
        
        function resize() {
            canvas.width = canvas.offsetWidth;
            canvas.height = canvas.offsetHeight;
        }
        window.addEventListener('resize', resize);
        resize();

        const particles = [];
        const maxParticles = 60;

        const puffCanvas = document.createElement('canvas');
        puffCanvas.width = 200;
        puffCanvas.height = 200;
        const pCtx = puffCanvas.getContext('2d');
        const gradient = pCtx.createRadialGradient(100, 100, 0, 100, 100, 100);
        gradient.addColorStop(0, 'rgba(255, 230, 210, 0.5)');
        gradient.addColorStop(0.4, 'rgba(255, 230, 210, 0.2)');
        gradient.addColorStop(1, 'rgba(255, 230, 210, 0)');
        pCtx.fillStyle = gradient;
        pCtx.fillRect(0, 0, 200, 200);

        class Particle {
            constructor() { this.reset(true); }
            reset(randomY = false) {
                this.x = Math.random() * canvas.width;
                this.y = randomY ? Math.random() * canvas.height : canvas.height * 0.7 + Math.random() * (canvas.height * 0.3);
                this.size = Math.random() * 300 + 200;
                this.vx = (Math.random() - 0.5) * 2.0;
                this.vy = -(Math.random() * 3.0 + 1.5);
                this.opacity = Math.random() * 0.5 + 0.1;
                this.life = 0;
                this.maxLife = Math.random() * 250 + 150;
            }
            update() {
                this.x += this.vx;
                this.y += this.vy;
                this.size += 1.5;
                this.life++;
                if (this.life >= this.maxLife || this.y < -this.size) this.reset();
            }
            draw() {
                const lifeRatio = this.life / this.maxLife;
                let alpha = this.opacity;
                if (lifeRatio < 0.2) alpha = this.opacity * (lifeRatio / 0.2);
                else if (lifeRatio > 0.8) alpha = this.opacity * ((1 - lifeRatio) / 0.2);
                ctx.globalAlpha = alpha;
                ctx.drawImage(puffCanvas, this.x - this.size / 2, this.y - this.size / 2, this.size, this.size);
            }
        }
        for (let i = 0; i < maxParticles; i++) particles.push(new Particle());
        function animate() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);
            particles.forEach(p => { p.update(); p.draw(); });
            requestAnimationFrame(animate);
        }
        animate();
    }

    function initSnapOnScroll() {
        gsap.registerPlugin(ScrollTrigger);

        // Initial Reveal Animation
        gsap.to(".cloud-door-left", { scaleX: 0, duration: 1.5, ease: "power3.inOut", delay: 0.2 });
        gsap.to(".cloud-door-right", { scaleX: 0, duration: 1.5, ease: "power3.inOut", delay: 0.2 });
        
        gsap.from(".p-layer-text > div", {
            y: "30vh",
            opacity: 0,
            duration: 2.0,
            ease: "power3.out",
            delay: 0.5
        });

        // Mouse Move Parallax
        if (window.innerWidth > 768) {
            const vp = document.querySelector('.parable-hero');
            const layers = vp.querySelectorAll('.p-layer[data-depth]');
            let mx = 0, my = 0, tx = 0, ty = 0, raf = null;

            vp.addEventListener('mousemove', e => {
                mx = (e.clientX / window.innerWidth  - 0.5) * 2;
                my = (e.clientY / window.innerHeight - 0.5) * 2;
                if (!raf) raf = requestAnimationFrame(tick);
            });
            vp.addEventListener('mouseleave', () => { mx = 0; my = 0; });

            function tick() {
                raf = null;
                tx += (mx - tx) * 0.07;
                ty += (my - ty) * 0.07;
                layers.forEach(layer => {
                    const depth = parseFloat(layer.dataset.depth) || 0.5;
                    const sx = depth * 25;
                    const sy = depth * 15;
                    gsap.set(layer, { x: tx * sx, y: ty * sy });
                });
                if (Math.abs(mx - tx) > 0.001 || Math.abs(my - ty) > 0.001) raf = requestAnimationFrame(tick);
            }
        }

        // ScrollTrigger Parallax
        let parableTl = gsap.timeline({
            scrollTrigger: {
                trigger: ".parable-hero",
                start: "top top",
                end: "+=150%", 
                scrub: true,
                pin: true,
                pinSpacing: false
            }
        });

        parableTl.to(".p-layer-text", { yPercent: -100, opacity: 0, scale: 0.9 }, 0); 
        parableTl.to(".p-layer-sky", { yPercent: -2 }, 0); 
        parableTl.to(".p-layer-back", { yPercent: -5 }, 0); 
        parableTl.to(".p-layer-mid", { yPercent: -10 }, 0); 
        parableTl.to(".p-layer-facts, .p-layer-indicator", { yPercent: -30, opacity: 0 }, 0);
        parableTl.to(".p-layer-foremost", { yPercent: -20 }, 0); 
    }

    document.addEventListener("DOMContentLoaded", () => {
        initValleyClouds();
        initSnapOnScroll();
    });
</script>
</body>
"""

script_start = content.rfind("<script>")
content = content[:script_start] + js_inject + content[content.rfind("</html>"):]

with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated destinations.php")
