import re

fpath = r'g:\Antigravity\leisure_loop_site\public\package-detail.php'

with open(fpath, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add CSS
css_to_add = """
    /* Glassmorphism Accordion */
    .glass-acc-container {
        display: flex;
        flex-direction: column;
        gap: 1.2rem;
    }
    .glass-acc-item {
        background: rgba(255, 255, 255, 0.02);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(197, 160, 89, 0.15);
        border-radius: 16px;
        transition: all 0.3s ease;
        overflow: hidden;
    }
    .glass-acc-item:hover {
        border-color: rgba(197, 160, 89, 0.3);
    }
    .glass-acc-trigger {
        width: 100%;
        background: none;
        border: none;
        cursor: pointer;
        padding: 1.5rem 1.8rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        text-align: left;
    }
    .glass-acc-left {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }
    .glass-acc-num {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: rgba(197, 160, 89, 0.15);
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--gold);
        font-weight: 700;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .glass-acc-title-group {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }
    .glass-acc-title {
        font-family: 'Inter', sans-serif;
        font-size: 1.1rem;
        color: white;
        font-weight: 600;
    }
    .glass-acc-subtitle {
        font-size: 0.85rem;
        color: #94a3b8;
    }
    .glass-acc-chevron {
        color: var(--gold);
        transition: transform 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .glass-acc-body {
        padding: 0 1.8rem 0 5.5rem;
        overflow: hidden;
        max-height: 0;
        opacity: 0;
        transition: max-height 0.4s ease, padding 0.3s ease, opacity 0.3s ease;
    }
"""

if '.glass-acc-container' not in content:
    content = content.replace('/* Responsive adjustments */', css_to_add + '\n    /* Responsive adjustments */')

# 2. Update HTML
html_old = """                <div id="itinerary-accordion" style="border-top: 1px solid rgba(197,160,89,0.15);">
                    <?php foreach ($itinerary as $i => $day): ?>
                    <div class="acc-item" style="border-bottom: 1px solid rgba(197,160,89,0.12);">
                        <button class="acc-trigger" data-index="<?php echo $i; ?>" data-coords="<?php echo htmlspecialchars($day['coords'] ?? ''); ?>" onclick="toggleAccordion(this)" style="width: 100%; background: none; border: none; cursor: pointer; padding: 1.5rem 0; display: flex; align-items: center; gap: 1.2rem; text-align: left;">
                            <!-- Circle +/- icon -->
                            <span class="acc-icon" style="flex-shrink: 0; width: 32px; height: 32px; border-radius: 50%; background: rgba(197,160,89,0.12); border: 1.5px solid rgba(197,160,89,0.4); display: flex; align-items: center; justify-content: center; color: var(--gold); font-size: 1.3rem; font-weight: 300; line-height: 1; transition: all 0.3s;">+</span>
                            <!-- Day label + title -->
                            <div>
                                <span style="font-size: 0.75rem; color: #64748b; letter-spacing: 0.1em; text-transform: uppercase; display: block;">Day <?php echo htmlspecialchars((string)($day['day'] ?? $i+1)); ?></span>
                                <span class="acc-title" style="font-family: 'Inter', sans-serif; font-size: 1.05rem; color: #e2e8f0; font-weight: 500;"><?php echo htmlspecialchars($day['title'] ?? 'Signature Experience'); ?></span>
                            </div>
                        </button>
                        <!-- Collapsible description -->
                        <div class="acc-body" style="overflow: hidden; max-height: 0; transition: max-height 0.4s ease, padding 0.3s ease; padding: 0 0 0 3.2rem;">
                            <p style="color: #94a3b8; font-size: 0.95rem; line-height: 1.8; padding-bottom: 1.5rem;">
                                <?php echo nl2br(htmlspecialchars($day['desc'] ?? 'Details coming soon.')); ?>
                            </p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>"""

html_new = """                <div id="itinerary-accordion" class="glass-acc-container">
                    <?php foreach ($itinerary as $i => $day): ?>
                    <div class="glass-acc-item">
                        <button class="glass-acc-trigger" data-index="<?php echo $i; ?>" data-coords="<?php echo htmlspecialchars($day['coords'] ?? ''); ?>" onclick="toggleAccordion(this)">
                            <div class="glass-acc-left">
                                <span class="glass-acc-num"><?php echo str_pad((string)($day['day'] ?? $i+1), 2, '0', STR_PAD_LEFT); ?></span>
                                <div class="glass-acc-title-group">
                                    <span class="glass-acc-title"><?php echo htmlspecialchars($day['title'] ?? 'Signature Experience'); ?></span>
                                    <span class="glass-acc-subtitle">Day <?php echo htmlspecialchars((string)($day['day'] ?? $i+1)); ?> Experience</span>
                                </div>
                            </div>
                            <span class="glass-acc-chevron">
                                <svg style="width:24px;height:24px;fill:currentColor;" viewBox="0 0 24 24"><path d="M7.41 8.59L12 13.17l4.59-4.58L18 10l-6 6-6-6 1.41-1.41z"/></svg>
                            </span>
                        </button>
                        <div class="glass-acc-body">
                            <p style="color: #94a3b8; font-size: 0.95rem; line-height: 1.8; padding-bottom: 1.5rem;">
                                <?php echo nl2br(htmlspecialchars($day['desc'] ?? 'Details coming soon.')); ?>
                            </p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>"""

content = content.replace(html_old, html_new)

# 3. Update JS toggleAccordion
js_old = """            // ── Accordion toggle ─────────────────────────────────────────────
            function toggleAccordion(btn) {
                const item = btn.closest('.acc-item');
                const body = item.querySelector('.acc-body');
                const icon = btn.querySelector('.acc-icon');
                const titleEl = btn.querySelector('.acc-title');
                const isOpen = body.style.maxHeight !== '0px' && body.style.maxHeight !== '';

                // Close all
                document.querySelectorAll('.acc-body').forEach(b => { b.style.maxHeight = '0'; b.style.paddingBottom = '0'; });
                document.querySelectorAll('.acc-icon').forEach(ic => { ic.textContent = '+'; ic.style.background = 'rgba(197,160,89,0.12)'; ic.style.color = 'var(--gold)'; });
                document.querySelectorAll('.acc-title').forEach(t => t.style.color = '#e2e8f0');

                if (!isOpen) {
                    body.style.maxHeight = body.scrollHeight + 'px';
                    icon.textContent = '−';
                    icon.style.background = 'var(--gold)';
                    icon.style.color = '#000';
                    titleEl.style.color = 'var(--gold)';

                    // Trigger map animation
                    const coords = btn.dataset.coords;
                    if (coords && coords.trim()) {
                        const p = coords.split(',');
                        if (p.length === 2) animateCarTo(parseFloat(p[0]), parseFloat(p[1]));
                    } else {
                        // fallback: move to indexed waypoint if available
                        const idx = parseInt(btn.dataset.index || 0);
                        if (waypoints[idx]) animateCarTo(waypoints[idx].lat, waypoints[idx].lng);
                    }
                }
            }"""

js_new = """            // ── Accordion toggle ─────────────────────────────────────────────
            function toggleAccordion(btn) {
                const item = btn.closest('.glass-acc-item');
                const body = item.querySelector('.glass-acc-body');
                const chevron = btn.querySelector('.glass-acc-chevron');
                const isOpen = item.classList.contains('active');

                // Close all
                document.querySelectorAll('.glass-acc-item').forEach(i => {
                    i.classList.remove('active');
                    const b = i.querySelector('.glass-acc-body');
                    if (b) { b.style.maxHeight = '0'; }
                });

                if (!isOpen) {
                    item.classList.add('active');
                    body.style.maxHeight = body.scrollHeight + 'px';

                    // Trigger map animation
                    const coords = btn.dataset.coords;
                    if (coords && coords.trim()) {
                        const p = coords.split(',');
                        if (p.length === 2) animateCarTo(parseFloat(p[0]), parseFloat(p[1]));
                    } else {
                        // fallback: move to indexed waypoint if available
                        const idx = parseInt(btn.dataset.index || 0);
                        if (waypoints[idx]) animateCarTo(waypoints[idx].lat, waypoints[idx].lng);
                    }
                }
            }"""

content = content.replace(js_old, js_new)

# Update first trigger call
content = content.replace(".querySelector('.acc-trigger')", ".querySelector('.glass-acc-trigger')")

with open(fpath, 'w', encoding='utf-8') as f:
    f.write(content)

print("Patching complete.")
