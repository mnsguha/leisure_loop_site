import os
import re

file_path = r'g:\Antigravity\leisure_loop_site\public\index.php'

# Read with latin-1 to avoid decode errors
with open(file_path, 'r', encoding='latin-1') as f:
    content = f.read()

# Define the clean stats section
clean_stats = """    <!-- =========================================================== -->
    <!-- STATS / TRUST STRIP                                    -->
    <!-- =========================================================== -->
    <section class="stats-strip">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 22C17.5228 22 22 17.5228 22 12C22 6.47715 17.5228 2 12 2C6.47715 2 2 6.47715 2 12C2 17.5228 6.47715 22 12 22Z"/><path d="M12 2V22"/><path d="M2 12H22"/><path d="M12 2C14.5013 4.73835 15.9228 8.29203 15.9228 12C15.9228 15.708 14.5013 19.2616 12 22"/><path d="M12 2C9.49872 4.73835 8.07725 8.29203 8.07725 12C8.07725 15.708 9.49872 19.2616 12 22"/></svg>
                    </div>
                    <span class="stat-number">500+</span>
                    <span class="stat-label">Bespoke Journeys</span>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24"><path d="M6 9H18L19 21H5L6 9Z"/><path d="M6 9C6 9 3 9 3 13C3 17 6 17 6 17"/><path d="M18 9C18 9 21 9 21 13C21 17 18 17 18 17"/><path d="M12 2V6"/></svg>
                    </div>
                    <span class="stat-number">12+</span>
                    <span class="stat-label">Years Excellence</span>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 2L15.09 8.26L22 9.27L17 14.14L18.18 21.02L12 17.77L5.82 21.02L7 14.14L2 9.27L8.91 8.26L12 2Z"/></svg>
                    </div>
                    <span class="stat-number">4.9</span>
                    <span class="stat-label">Client Rating</span>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24"><path d="M12 15L17 18V5H7V18L12 15Z"/><path d="M12 2V5"/></svg>
                    </div>
                    <span class="stat-number">Elite</span>
                    <span class="stat-label">Award Winner 2024</span>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24"><path d="M20.84 4.61C20.3292 4.09904 19.7228 3.69363 19.0554 3.41708C18.388 3.14053 17.6725 2.99816 16.95 3C15.4812 3.0001 14.0728 3.5828 13.03 4.62L12 5.67L10.97 4.63C9.92723 3.58723 8.51278 3.00008 7.045 3C6.32247 2.99816 5.60703 3.14053 4.93963 3.41708C4.27222 3.69363 3.6658 4.09904 3.155 4.61C1.047 6.718 1.047 10.138 3.155 12.246L12 21.091L20.845 12.246C22.953 10.138 22.953 6.718 20.84 4.61Z"/></svg>
                    </div>
                    <span class="stat-number">99%</span>
                    <span class="stat-label">Happy Explorers</span>
                </div>
                <div class="stat-item">
                    <div class="stat-icon">
                        <svg viewBox="0 0 24 24"><path d="M3 18H21"/><path d="M12 18V4"/><path d="M8 4H16"/><path d="M3 14C3 14 3 6 12 6C21 6 21 14 21 14"/></svg>
                    </div>
                    <span class="stat-number">24/7</span>
                    <span class="stat-label">Priority Support</span>
                </div>
            </div>
        </div>
    </section>"""

# Replace the messy part
# The regex handles potential non-ascii characters in the search range
pattern = re.compile(r'</header>.*?<section class="packages-section"', re.DOTALL)
new_content = pattern.sub('</header>\n\n' + clean_stats + '\n\n    <section class="packages-section"', content)

# Write back as UTF-8 (it will fix the characters)
with open(file_path, 'w', encoding='utf-8') as f:
    f.write(new_content)

print("Cleanup complete.")
