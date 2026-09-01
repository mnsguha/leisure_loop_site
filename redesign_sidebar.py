import sys

sidebar_path = "public/admin/sidebar.php"
css_path = "public/css/admin.css"

sidebar_content = """<?php
$currentPage = basename($_SERVER['PHP_SELF']);

// Determine which parent category should be expanded based on the current page
$activeCategory = '';
if (in_array($currentPage, ['packages.php', 'fixed_departures.php', 'fixed_departure-form.php', 'destinations.php', 'themes.php'])) {
    $activeCategory = 'packages';
} elseif (in_array($currentPage, ['hotels.php', 'hotel-form.php', 'hotel-rooms.php', 'hotel-bookings.php'])) {
    $activeCategory = 'hotels';
} elseif (in_array($currentPage, ['cab_classes.php', 'cab_class-form.php', 'vehicles.php', 'vehicle-form.php'])) {
    $activeCategory = 'cabs';
} elseif (in_array($currentPage, ['leads.php', 'subscribers.php'])) {
    $activeCategory = 'leads';
} elseif (in_array($currentPage, ['gallery.php', 'testimonials.php', 'testimonial-form.php', 'blogs.php', 'blog-form.php', 'process.php'])) {
    $activeCategory = 'content';
} elseif (in_array($currentPage, ['settings.php', 'marquee.php', 'popup.php', 'advertisement.php', 'accreditations.php', 'hotel_partners.php'])) {
    $activeCategory = 'settings';
}
?>
<aside class="sidebar">
    <div class="logo">
        <img src="../assets/img/leisure.png" alt="Leisure Loop Admin" style="max-width: 100%; height: auto; max-height: 50px;">
    </div>
    <ul class="sidebar-nav">
        <li><a href="index.php" class="<?php echo $currentPage == 'index.php' ? 'active' : ''; ?>">Dashboard</a></li>
        
        <!-- Packages & Destinations -->
        <li class="nav-group <?php echo $activeCategory == 'packages' ? 'expanded' : ''; ?>">
            <div class="nav-group-header" onclick="toggleNavGroup(this)">
                <span>Packages &amp; Destinations</span>
                <span class="nav-chevron">▼</span>
            </div>
            <ul class="submenu">
                <li><a href="packages.php" class="<?php echo $currentPage == 'packages.php' ? 'active' : ''; ?>">Manage Packages</a></li>
                <li><a href="fixed_departures.php" class="<?php echo in_array($currentPage, ['fixed_departures.php', 'fixed_departure-form.php']) ? 'active' : ''; ?>">Fixed Departures</a></li>
                <li><a href="destinations.php" class="<?php echo $currentPage == 'destinations.php' ? 'active' : ''; ?>">Manage Destinations</a></li>
                <li><a href="themes.php" class="<?php echo $currentPage == 'themes.php' ? 'active' : ''; ?>">Travel Themes</a></li>
            </ul>
        </li>

        <!-- Hotels -->
        <li class="nav-group <?php echo $activeCategory == 'hotels' ? 'expanded' : ''; ?>">
            <div class="nav-group-header" onclick="toggleNavGroup(this)">
                <span>Hotels</span>
                <span class="nav-chevron">▼</span>
            </div>
            <ul class="submenu">
                <li><a href="hotels.php" class="<?php echo in_array($currentPage, ['hotels.php', 'hotel-form.php', 'hotel-rooms.php']) ? 'active' : ''; ?>">Manage Hotels</a></li>
                <li><a href="hotel-bookings.php" class="<?php echo $currentPage == 'hotel-bookings.php' ? 'active' : ''; ?>">Hotel Bookings</a></li>
            </ul>
        </li>

        <!-- Cabs -->
        <li class="nav-group <?php echo $activeCategory == 'cabs' ? 'expanded' : ''; ?>">
            <div class="nav-group-header" onclick="toggleNavGroup(this)">
                <span>Cabs</span>
                <span class="nav-chevron">▼</span>
            </div>
            <ul class="submenu">
                <li><a href="cab_classes.php" class="<?php echo in_array($currentPage, ['cab_classes.php', 'cab_class-form.php']) ? 'active' : ''; ?>">Manage Cab Classes</a></li>
                <li><a href="vehicles.php" class="<?php echo in_array($currentPage, ['vehicles.php', 'vehicle-form.php']) ? 'active' : ''; ?>">Manage Vehicles</a></li>
            </ul>
        </li>

        <!-- Leads & Marketing -->
        <li class="nav-group <?php echo $activeCategory == 'leads' ? 'expanded' : ''; ?>">
            <div class="nav-group-header" onclick="toggleNavGroup(this)">
                <span>Leads &amp; Marketing</span>
                <span class="nav-chevron">▼</span>
            </div>
            <ul class="submenu">
                <li><a href="leads.php" class="<?php echo $currentPage == 'leads.php' ? 'active' : ''; ?>">Inquiries</a></li>
                <li><a href="subscribers.php" class="<?php echo $currentPage == 'subscribers.php' ? 'active' : ''; ?>">Newsletter Subscribers</a></li>
            </ul>
        </li>

        <!-- Website Content -->
        <li class="nav-group <?php echo $activeCategory == 'content' ? 'expanded' : ''; ?>">
            <div class="nav-group-header" onclick="toggleNavGroup(this)">
                <span>Website Content</span>
                <span class="nav-chevron">▼</span>
            </div>
            <ul class="submenu">
                <li><a href="gallery.php" class="<?php echo $currentPage == 'gallery.php' ? 'active' : ''; ?>">Guest Gallery</a></li>
                <li><a href="testimonials.php" class="<?php echo in_array($currentPage, ['testimonials.php', 'testimonial-form.php']) ? 'active' : ''; ?>">Testimonials</a></li>
                <li><a href="blogs.php" class="<?php echo in_array($currentPage, ['blogs.php', 'blog-form.php']) ? 'active' : ''; ?>">Journal (Blog)</a></li>
                <li><a href="process.php" class="<?php echo $currentPage == 'process.php' ? 'active' : ''; ?>">How We Work</a></li>
            </ul>
        </li>

        <!-- Site Configurations -->
        <li class="nav-group <?php echo $activeCategory == 'settings' ? 'expanded' : ''; ?>">
            <div class="nav-group-header" onclick="toggleNavGroup(this)">
                <span>Site Configurations</span>
                <span class="nav-chevron">▼</span>
            </div>
            <ul class="submenu">
                <li><a href="settings.php" class="<?php echo $currentPage == 'settings.php' ? 'active' : ''; ?>">Site Settings</a></li>
                <li><a href="marquee.php" class="<?php echo $currentPage == 'marquee.php' ? 'active' : ''; ?>">Film Strip Roll</a></li>
                <li><a href="popup.php" class="<?php echo $currentPage == 'popup.php' ? 'active' : ''; ?>">Notice Popup</a></li>
                <li><a href="advertisement.php" class="<?php echo $currentPage == 'advertisement.php' ? 'active' : ''; ?>">Advertisement</a></li>
                <li><a href="accreditations.php" class="<?php echo $currentPage == 'accreditations.php' ? 'active' : ''; ?>">Trusted &amp; Accredited</a></li>
                <li><a href="hotel_partners.php" class="<?php echo $currentPage == 'hotel_partners.php' ? 'active' : ''; ?>">Hotel Partners</a></li>
            </ul>
        </li>

        <li style="margin-top: 3rem;"><a href="logout.php">Logout</a></li>
    </ul>
</aside>

<script>
function toggleNavGroup(element) {
    const parentLi = element.parentElement;
    parentLi.classList.toggle('expanded');
}
</script>
"""

with open(sidebar_path, "w", encoding="utf-8") as f:
    f.write(sidebar_content)

css_append = """
/* ─── SIDEBAR NAV GROUPS ─── */
.nav-group {
    margin-bottom: 0.25rem;
}

.nav-group-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.85rem 1.25rem;
    color: var(--admin-text-muted);
    font-weight: 600;
    font-size: 0.8rem;
    cursor: pointer;
    border-radius: 12px;
    transition: all 0.3s var(--admin-ease);
    text-transform: uppercase;
    letter-spacing: 0.05em;
    user-select: none;
}

.nav-group-header:hover {
    color: var(--admin-text);
    background: rgba(255, 255, 255, 0.02);
}

.nav-chevron {
    font-size: 0.7rem;
    transition: transform 0.3s var(--admin-ease);
}

.nav-group.expanded .nav-chevron {
    transform: rotate(180deg);
}

.nav-group.expanded .nav-group-header {
    color: var(--admin-gold);
}

.submenu {
    list-style: none;
    padding: 0;
    margin: 0;
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.4s var(--admin-ease), opacity 0.4s var(--admin-ease);
    opacity: 0;
}

.nav-group.expanded .submenu {
    max-height: 500px;
    opacity: 1;
    margin-top: 0.25rem;
}

.submenu a {
    padding: 0.65rem 1.25rem 0.65rem 2.5rem !important;
    font-size: 0.85rem !important;
}
"""

with open(css_path, "a", encoding="utf-8") as f:
    f.write(css_append)

print("Sidebar redesigned successfully!")
