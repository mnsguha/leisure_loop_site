<aside class="sidebar">
    <div class="logo">
        <span class="logo-text">ADMIN <span class="accent">LOOP</span></span>
    </div>
    <ul class="sidebar-nav">
        <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Dashboard</a></li>
        <li><a href="packages.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'packages.php' ? 'active' : ''; ?>">Manage Packages</a></li>
        <li><a href="destinations.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'destinations.php' ? 'active' : ''; ?>">Manage Destinations</a></li>
        <li><a href="themes.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'themes.php' ? 'active' : ''; ?>">Travel Themes</a></li>
        <li><a href="leads.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'leads.php' ? 'active' : ''; ?>">Inquiries</a></li>
        <li><a href="blogs.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'blogs.php' || basename($_SERVER['PHP_SELF']) == 'blog-form.php' ? 'active' : ''; ?>">Journal (Blog)</a></li>
        <li><a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">Site Settings</a></li>
        <li><a href="process.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'process.php' ? 'active' : ''; ?>">How We Work</a></li>
        <li><a href="marquee.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'marquee.php' ? 'active' : ''; ?>">Film Strip Roll</a></li>
        <li><a href="popup.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'popup.php' ? 'active' : ''; ?>">Notice Popup</a></li>
        <li><a href="advertisement.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'advertisement.php' ? 'active' : ''; ?>">Advertisement</a></li>
        <li><a href="accreditations.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'accreditations.php' ? 'active' : ''; ?>">Trusted &amp; Accredited</a></li>
        <li style="margin-top: 5rem;"><a href="logout.php">Logout</a></li>
    </ul>
</aside>
