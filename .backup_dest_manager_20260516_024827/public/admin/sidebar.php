<aside class="sidebar">
    <div class="logo">
        <span class="logo-text">ADMIN <span class="accent">LOOP</span></span>
    </div>
    <ul class="sidebar-nav">
        <li><a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">Dashboard</a></li>
        <li><a href="packages.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'packages.php' ? 'active' : ''; ?>">Manage Packages</a></li>
        <li><a href="leads.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'leads.php' ? 'active' : ''; ?>">Inquiries</a></li>
        <li><a href="settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">Site Settings</a></li>
        <li><a href="marquee.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'marquee.php' ? 'active' : ''; ?>">Film Strip Roll</a></li>
        <li style="margin-top: 5rem;"><a href="logout.php">Logout</a></li>
    </ul>
</aside>
