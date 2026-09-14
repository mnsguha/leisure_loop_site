<?php
// Shared Mobile Header Partial - Used on Catalog & Landing Pages Only
$header_title = $mobile_header_title ?? 'Leisure Loop';
$active_tab   = $mobile_active_nav ?? '';
$back_target  = $back_url ?? 'index.php?view=mobile';
?>

<!-- Top App Header -->
<header class="app-header" id="appHeader">
    <a href="<?= htmlspecialchars($back_target) ?>" class="header-btn" aria-label="Go Back">
        <span class="material-symbols-outlined">arrow_back</span>
    </a>

    <h1 class="m-header-title"><?= htmlspecialchars($header_title) ?></h1>

    <button type="button" class="header-btn" data-action="open-enquiry-modal" aria-label="Concierge Support">
        <span class="material-symbols-outlined">support_agent</span>
    </button>
</header>

<!-- Horizontal Service Navigation Bar -->
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
