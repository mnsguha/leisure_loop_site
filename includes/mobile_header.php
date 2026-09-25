<?php
// Shared Mobile Header Partial - Used on Catalog & Landing Pages Only
$header_title = $mobile_header_title ?? 'Leisure Loop';
$active_tab   = $mobile_active_nav ?? '';
?>

<!-- Horizontal Service Navigation Bar -->
<?php if (!isset($hide_mobile_services_nav) || !$hide_mobile_services_nav): ?>
<nav class="m-services-nav">
    <?php
    $services = [
        ['key' => 'offers', 'label' => 'Offers', 'icon' => 'local_offer', 'url' => 'offers.php?view=mobile', 'mod' => 'offers'],
        ['key' => 'tours', 'label' => 'Tours', 'icon' => 'luggage', 'url' => 'packages.php?view=mobile', 'mod' => 'tours'],
        ['key' => 'destinations', 'label' => 'Destinations', 'icon' => 'explore', 'url' => 'destinations.php?view=mobile', 'mod' => 'dest'],
        ['key' => 'fixed', 'label' => 'Fixed Dept.', 'icon' => 'calendar_month', 'url' => 'fixed-departures.php?view=mobile', 'mod' => 'fixed'],
        ['key' => 'hotels', 'label' => 'Hotels', 'icon' => 'hotel', 'url' => 'hotels.php?view=mobile', 'mod' => 'hotels'],
        ['key' => 'cabs', 'label' => 'Cabs', 'icon' => 'directions_car', 'url' => 'cabs.php?view=mobile', 'mod' => 'cabs']
    ];
    foreach ($services as $s):
        $is_active = ($active_tab === $s['key']) ? 'active' : '';
    ?>
    <a href="<?= $s['url'] ?>" class="m-service-item <?= $is_active ?>">
        <span class="material-symbols-outlined m-service-icon m-service-icon--<?= $s['mod'] ?>"><?= $s['icon'] ?></span>
        <span class="m-service-label"><?= $s['label'] ?></span>
    </a>
    <?php endforeach; ?>
</nav>
<?php endif; ?>

<h1 class="sr-only"><?= htmlspecialchars($header_title) ?></h1>
