import re

file_path = 'includes/mobile_home.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

# Replace responsive-package-card with mini-package-card
content = content.replace('class="package-card responsive-package-card"', 'class="package-card mini-package-card"')

# Add the mini-package-card CSS
css_to_add = """
                    /* Mini Package Cards (3 per screen) */
                    .mini-package-card {
                        flex: 0 0 31% !important;
                        min-width: 31% !important;
                        max-width: 31% !important;
                        overflow: hidden;
                        transform: translateZ(0);
                        cursor: default;
                        border-radius: 12px;
                    }
                    .mini-package-card .pkg-img {
                        border-radius: 12px 12px 0 0 !important;
                        height: 140px;
                    }
                    .mini-package-card .pkg-img-bg {
                        border-radius: 12px 12px 0 0 !important;
                    }
                    .mini-package-card .pkg-badge {
                        top: 6px !important;
                        left: 6px !important;
                        font-size: 0.45rem !important;
                        padding: 3px 6px !important;
                        border-radius: 8px !important;
                        letter-spacing: 0.05em !important;
                    }
                    .mini-package-card .pkg-overlay-content {
                        padding: 8px !important;
                    }
                    .mini-package-card .pkg-dest {
                        font-size: 0.45rem !important;
                        margin-bottom: 2px !important;
                        letter-spacing: 0.05em !important;
                    }
                    .mini-package-card .pkg-title {
                        font-size: 0.75rem !important;
                        line-height: 1.2 !important;
                        margin-bottom: 0 !important;
                    }
                    .mini-package-card .pkg-footer {
                        padding: 8px 8px !important;
                        border-radius: 0 0 12px 12px !important;
                    }
                    .mini-package-card .pkg-price {
                        font-size: 0.55rem !important;
                    }
                    .mini-package-card .pkg-price b {
                        font-size: 0.65rem !important;
                    }
                    .mini-package-card .pkg-btn {
                        width: 20px !important;
                        height: 20px !important;
                        font-size: 10px !important;
                    }
"""

if '.mini-package-card' not in content:
    content = content.replace('/* Responsive Package Cards */', css_to_add + '\n                    /* Responsive Package Cards */')

# Change gap
content = content.replace('style="padding-top: 20px; gap: 30px !important;"', 'style="padding-top: 20px; gap: 12px !important;"')

# Shorten badge text logic
old_badge_logic = """                            <?php 
                            if (!empty($pkg['nights']) && !empty($pkg['days'])) {
                                echo str_pad($pkg['nights'], 2, '0', STR_PAD_LEFT) . " NIGHTS / " . str_pad($pkg['days'], 2, '0', STR_PAD_LEFT) . " DAYS";
                            } else {
                                $itinerary = json_decode($pkg['itinerary'], true);
                                if ($itinerary && is_array($itinerary)) {
                                    $days = count($itinerary);
                                    $nights = max(1, $days - 1);
                                    echo str_pad($nights, 2, '0', STR_PAD_LEFT) . " NIGHTS / " . str_pad($days, 2, '0', STR_PAD_LEFT) . " DAYS";
                                } else {
                                    echo "CUSTOM DURATION";
                                }
                            }
                            ?>"""

new_badge_logic = """                            <?php 
                            if (!empty($pkg['nights']) && !empty($pkg['days'])) {
                                echo $pkg['nights'] . "N / " . $pkg['days'] . "D";
                            } else {
                                $itinerary = json_decode($pkg['itinerary'], true);
                                if ($itinerary && is_array($itinerary)) {
                                    $days = count($itinerary);
                                    $nights = max(1, $days - 1);
                                    echo $nights . "N / " . $days . "D";
                                } else {
                                    echo "CUSTOM";
                                }
                            }
                            ?>"""

content = content.replace(old_badge_logic, new_badge_logic)

with open(file_path, 'w', encoding='utf-8') as f:
    f.write(content)

print("Updated cards successfully.")
