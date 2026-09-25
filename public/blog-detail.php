<?php
require_once '../config/db.php';
require_once '../includes/functions.php';
csrf_stamp_form();
require_once '../includes/header.php';

$slug = isset($_GET['slug']) ? trim($_GET['slug']) : '';
$blog = null;

if ($slug && $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM blogs WHERE slug = ? AND is_published = 1");
        $stmt->execute([$slug]);
        $blog = $stmt->fetch();
    } catch (PDOException $e) {}
}

if (!$blog) {
    echo "<div class='container' style='padding: 10rem 0; text-align: center;'><h1 style='color: var(--gold);'>Article Not Found</h1><p style='color: var(--text-muted);'>The article you're looking for may have been moved or unpublished.</p><a href='blog.php' class='btn-primary' style='margin-top: 2rem; display: inline-block;'>Return to Journal</a></div>";
    require_once '../includes/footer.php';
    exit;
}

// Fetch all active destinations to populate the sidebar selector dynamically
$destinations = [];
if ($pdo) {
    try {
        $destinations = $pdo->query("SELECT name FROM destinations WHERE is_active = 1 ORDER BY name ASC")->fetchAll();
    } catch (PDOException $e) {}
}

$img = !empty($blog['image_url']) ? $blog['image_url'] : 'https://images.unsplash.com/photo-1544644181-1484b3fdfc62?q=80&w=2000';
$img = strpos($img, 'http') === 0 ? $img : $img;
?>

<!-- Article Hero -->
<section class="article-hero" style="position: relative; height: 50vh; min-height: 350px; display: flex; align-items: flex-end; padding-bottom: 3rem;">
    <div class="article-hero-bg" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-image: url('<?php echo htmlspecialchars($img); ?>'); background-size: cover; background-position: center; z-index: 1;"></div>
    <div class="article-hero-overlay" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; background: linear-gradient(to top, #050a14 0%, rgba(5,10,20,0.7) 40%, rgba(5,10,20,0.3) 100%); z-index: 2;"></div>
    
    <div class="container" style="position: relative; z-index: 3; max-width: 1200px; margin: 0 auto;">
        <div class="article-meta" style="color: var(--gold); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.15em; margin-bottom: 1rem; font-weight: 600;">
            Journal &bull; <?php echo date('F j, Y', strtotime($blog['created_at'])); ?> &bull; <?php echo htmlspecialchars($blog['author']); ?>
        </div>
        <h1 class="article-title" style="font-family: 'Playfair Display', serif; font-size: 3rem; color: #fff; margin: 0; line-height: 1.2; max-width: 900px;">
            <?php echo htmlspecialchars($blog['title']); ?>
        </h1>
    </div>
</section>

<!-- Content Section with 2-Column Grid -->
<section style="padding: 5rem 0; background-color: var(--obsidian); border-top: 1px solid rgba(197, 160, 89, 0.1);">
    <div class="container" style="max-width: 1200px; margin: 0 auto;">
        
        <?php if (isset($_GET['lead_sent'])): ?>
            <div style="background: rgba(16, 185, 129, 0.1); border: 1px solid #10b981; border-radius: 12px; padding: 1.5rem; margin-bottom: 3rem; text-align: center; color: #ffffff;">
                <h4 style="color: #10b981; font-family: 'Playfair Display', serif; font-size: 1.4rem; margin: 0 0 0.5rem 0;">Bespoke Enquiry Received</h4>
                <p style="margin: 0; color: var(--text-muted);">Your private concierge will review your travel blueprint and reach out within 24 hours.</p>
            </div>
        <?php endif; ?>

        <div class="blog-detail-grid">
            
            <!-- Left Column: Article Body & Interactive TOC -->
            <div class="blog-main-content">
                
                <!-- Premium Image Container -->
                <div class="luxury-image-wrapper">
                    <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($blog['title']); ?>" class="luxury-cover-img">
                </div>

                <!-- Dynamic Table of Contents Accordion -->
                <div class="luxe-toc-container" id="luxeToc">
                    <div class="luxe-toc-header" data-action="toggle-toc">
                        <h4>
                            <svg class="luxe-trust-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="width:18px;height:18px;margin-top:2px;">
                                <line x1="4" y1="6" x2="20" y2="6"></line>
                                <line x1="4" y1="12" x2="20" y2="12"></line>
                                <line x1="4" y1="18" x2="20" y2="18"></line>
                            </svg>
                            Table of Contents
                        </h4>
                        <svg class="luxe-toc-chevron" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <ul class="luxe-toc-list" id="luxeTocList">
                        <!-- Dynamic list generated by JavaScript -->
                    </ul>
                </div>

                <!-- Main Body Content -->
                <div class="article-excerpt" style="font-size: 1.25rem; line-height: 1.8; color: var(--gold); font-family: 'Playfair Display', serif; font-style: italic; margin-bottom: 3rem; padding-bottom: 2rem; border-bottom: 1px solid rgba(197, 160, 89, 0.1);">
                    "<?php echo htmlspecialchars($blog['excerpt']); ?>"
                </div>

                <div class="article-body" id="articleBody" style="color: rgba(255, 255, 255, 0.85); font-size: 1.1rem; line-height: 1.95;">
                    <?php echo $blog['content']; /* Rich text formatted HTML */ ?>
                </div>

                <div class="article-footer" style="margin-top: 4rem; padding-top: 3rem; border-top: 1px solid rgba(197, 160, 89, 0.1); text-align: center;">
                    <a href="blog.php" class="btn-primary" style="padding: 0.8rem 2.2rem; border-radius: 50px;">Return to Journal</a>
                </div>

            </div>

            <!-- Right Column: Sticky Sidebar -->
            <div class="bespoke-enquiry-sidebar">
                
                <!-- 2-Step Enquiry Form -->
                <div class="glass-form-card">
                    <h3>Book Your Tour Now</h3>
                    <p style="color: var(--text-muted); font-size: 0.82rem; margin: 0;">Unlock private bookings & bespoke rates</p>
                    
                    <div class="step-indicator-bar">
                        <div class="step-node active" id="indicator1">1</div>
                        <div class="step-line"></div>
                        <div class="step-node" id="indicator2">2</div>
                    </div>

                    <form action="process-lead.php" method="POST" id="sidebarEnquiryForm">
<input type="text" name="fax_office" hidden tabindex="-1" autocomplete="off" aria-hidden="true">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                        <input type="hidden" name="source" value="Journal Sidebar Form">
                        
                        <!-- Step 1 Fields -->
                        <div id="formStep1">
                            <div class="glass-input-group">
                                <label for="destination">Select Destination *</label>
                                <select name="destination" id="destination" class="glass-input" required>
                                    <option value="" disabled selected>-- Select Place --</option>
                                    <?php foreach ($destinations as $dest): ?>
                                        <option value="<?php echo htmlspecialchars($dest['name']); ?>">
                                            <?php echo htmlspecialchars($dest['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                    <option value="Sikkim Expedition">Sikkim Expedition</option>
                                    <option value="Yumthang Valley Loop">Yumthang Valley Loop</option>
                                    <option value="North Sikkim Explorer">North Sikkim Explorer</option>
                                </select>
                            </div>
                            
                            <div class="glass-input-group">
                                <label for="adults">No. of Travellers *</label>
                                <input type="number" name="adults" id="adults" min="1" max="50" value="2" class="glass-input" required>
                            </div>
                            
                            <button type="button" class="btn-primary" style="width: 100%; padding: 0.9rem; border-radius: 8px; margin-top: 1rem; border: none; cursor: pointer; text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;" data-action="go-step" data-step="2">
                                Next <span style="margin-left: 5px;">&rarr;</span>
                            </button>
                        </div>

                        <!-- Step 2 Fields (Hidden Initially) -->
                        <div id="formStep2" style="display: none;">
                            <div class="glass-input-group">
                                <label for="date">Date of Travelling *</label>
                                <input type="date" name="date" id="date" class="glass-input" required>
                            </div>
                            
                            <div class="glass-input-group">
                                <label for="name">Your Name *</label>
                                <input type="text" name="name" id="name" placeholder="Full Name" class="glass-input" required>
                            </div>
                            
                            <div class="glass-input-group">
                                <label for="phone">Contact Number *</label>
                                <input type="tel" name="phone" id="phone" placeholder="e.g. +91 99999 99999" class="glass-input" required>
                            </div>
                            
                            <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                                <button type="button" class="btn-primary" style="background: rgba(255,255,255,0.05); color: #ffffff; border: 1px solid rgba(255,255,255,0.1); width: 40%; padding: 0.9rem; border-radius: 8px; cursor: pointer; font-weight: 700; text-transform: uppercase;" data-action="go-step" data-step="1">
                                    Back
                                </button>
                                <button type="submit" class="btn-primary" style="width: 60%; padding: 0.9rem; border-radius: 8px; border: none; cursor: pointer; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;">
                                    Enquire Now
                                </button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Elite Trust Badges Box -->
                <div class="luxe-trust-box">
                    <h4 class="luxe-trust-title">Why Book With Us?</h4>
                    <div class="luxe-trust-list">
                        
                        <div class="luxe-trust-item">
                            <svg class="luxe-trust-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                            </svg>
                            <div class="luxe-trust-text">
                                <h5>100% Secure Private Escort</h5>
                                <p>Premium high-altitude certified luxury escort and vetted 4x4 transport safety.</p>
                            </div>
                        </div>

                        <div class="luxe-trust-item">
                            <svg class="luxe-trust-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                            <div class="luxe-trust-text">
                                <h5>5-Star Handcrafted Itineraries</h5>
                                <p>Tailored pathways, elite hotel clusters, and local gourmet discoveries.</p>
                            </div>
                        </div>

                        <div class="luxe-trust-item">
                            <svg class="luxe-trust-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <div class="luxe-trust-text">
                                <h5>24/7 Handcrafted Concierge</h5>
                                <p>A dedicated curator on standby to adjust plans or cater to custom culinary needs.</p>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>

    </div>
</section>

<!-- Inline Styling for tinyMCE content -->
<link rel="stylesheet" href="css/content-pages.css">

<script src="js/modules/content-pages.js" defer></script>

<?php require_once '../includes/footer.php'; ?>
