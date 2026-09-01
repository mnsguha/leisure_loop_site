import re

fpath = r'g:\Antigravity\leisure_loop_site\public\package-detail.php'

with open(fpath, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace the HTML block
html_old = """                            <div class="glass-acc-left">
                                <span class="glass-acc-num"><?php echo str_pad((string)($day['day'] ?? $i+1), 2, '0', STR_PAD_LEFT); ?></span>
                                <div class="glass-acc-title-group">
                                    <span class="glass-acc-title"><?php echo htmlspecialchars($day['title'] ?? 'Signature Experience'); ?></span>
                                    <span class="glass-acc-subtitle">Day <?php echo htmlspecialchars((string)($day['day'] ?? $i+1)); ?> Experience</span>
                                </div>
                            </div>"""

html_new = """                            <div class="glass-acc-left">
                                <div style="display: flex; flex-direction: column; align-items: center; gap: 0.3rem; flex-shrink: 0;">
                                    <span style="font-size: 0.65rem; color: #94a3b8; letter-spacing: 0.15em; text-transform: uppercase; font-weight: 600;">Day</span>
                                    <span class="glass-acc-num" style="margin-top: -2px;"><?php echo str_pad((string)($day['day'] ?? $i+1), 2, '0', STR_PAD_LEFT); ?></span>
                                </div>
                                <div class="glass-acc-title-group">
                                    <span class="glass-acc-title"><?php echo htmlspecialchars($day['title'] ?? 'Signature Experience'); ?></span>
                                    <?php if (!empty($day['subtitle'])): ?>
                                    <span class="glass-acc-subtitle"><?php echo htmlspecialchars($day['subtitle']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </div>"""

content = content.replace(html_old, html_new)

with open(fpath, 'w', encoding='utf-8') as f:
    f.write(content)

print("Patch applied to package-detail.php")
