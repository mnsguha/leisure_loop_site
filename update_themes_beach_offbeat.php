<?php
// Find db.php wherever it exists in the tree
$paths = [
    __DIR__ . '/../config/db.php',
    __DIR__ . '/config/db.php',
    __DIR__ . '/leisure_loop_site/config/db.php',
    __DIR__ . '/../leisure_loop_site/config/db.php'
];
$db_found = false;
foreach ($paths as $p) {
    if (file_exists($p)) {
        require_once $p;
        $db_found = true;
        break;
    }
}
if (!$db_found && !isset($pdo)) {
    die("Could not locate config/db.php");
}

if (!$pdo) {
    try {
        $pdo = new PDO("mysql:host=127.0.0.1;dbname=leisure_loop_db;charset=utf8mb4", "root", "", [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (Exception $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

echo "<div style='font-family: Arial, sans-serif; max-width: 600px; margin: 40px auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; background: #0b1329; color: #fff;'>";
echo "<h2 style='color: #c5a059; margin-top: 0;'>✦ Leisure Loop Theme Updater ✦</h2>";

try {
    echo "<h3>1. Replacing 'Group' theme with 'Beach'</h3>";
    
    $stmt = $pdo->prepare("SELECT * FROM tour_categories WHERE name = 'Group'");
    $stmt->execute();
    $group_cat = $stmt->fetch();

    $beach_icon = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 12c-2 0-3-1-4.5-1S10 12 8 12s-3-1-4.5-1S1 12 1 12v6c0 1.5 2 2 3.5 2s2.5-1 4-1 2.5 1 4 1 2.5-1 4-1 2.5 1 3.5 1v-6c0 0-1-1-2.5-1s-2.5 1-4.5 1z"/><circle cx="12" cy="6" r="3"/><path d="M2 22h20"/></svg>';
    $beach_bg = 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';
    $beach_tagline = 'Pristine coastal bliss & tropical serenity';

    if ($group_cat) {
        $update_cat = $pdo->prepare("UPDATE tour_categories SET name = 'Beach', tagline = ?, image_url = ?, icon_svg = ? WHERE name = 'Group'");
        $update_cat->execute([$beach_tagline, $beach_bg, $beach_icon]);
        echo "<p style='color: #4ade80;'>✔ Updated 'Group' category to 'Beach' in tour_categories table.</p>";
    } else {
        $check_beach = $pdo->prepare("SELECT * FROM tour_categories WHERE name = 'Beach'");
        $check_beach->execute();
        if (!$check_beach->fetch()) {
            $insert = $pdo->prepare("INSERT INTO tour_categories (name, tagline, image_url, icon_svg, display_order, is_active) VALUES (?, ?, ?, ?, ?, 1)");
            $insert->execute(['Beach', $beach_tagline, $beach_bg, $beach_icon, 5]);
            echo "<p style='color: #4ade80;'>✔ Inserted 'Beach' into tour_categories table.</p>";
        } else {
            echo "<p style='color: #94a3b8;'>✔ 'Beach' already exists in tour_categories table.</p>";
        }
    }

    $pkgs = $pdo->query("SELECT id, tour_type FROM packages WHERE is_active = 1")->fetchAll();
    $pkg_updated = 0;
    foreach ($pkgs as $pkg) {
        if (!empty($pkg['tour_type'])) {
            $types = array_map('trim', explode(',', $pkg['tour_type']));
            $modified = false;
            foreach ($types as $idx => $tag) {
                if (strcasecmp($tag, 'Group') === 0 || strcasecmp($tag, 'Group Tour') === 0 || strcasecmp($tag, 'Group Tours') === 0) {
                    $types[$idx] = 'Beach';
                    $modified = true;
                }
            }
            if ($modified) {
                $new_type = implode(', ', array_unique($types));
                $up_pkg = $pdo->prepare("UPDATE packages SET tour_type = ? WHERE id = ?");
                $up_pkg->execute([$new_type, $pkg['id']]);
                $pkg_updated++;
            }
        }
    }
    echo "<p style='color: #4ade80;'>✔ Updated {$pkg_updated} package(s): replaced 'Group' tags with 'Beach'.</p>";

    echo "<h3 style='margin-top: 30px;'>2. Adding 'Off Beat' theme</h3>";

    $offbeat_name = 'Off Beat';
    $offbeat_tagline = 'Uncharted wilderness & secret local escapes';
    $offbeat_icon = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>';
    $offbeat_bg = 'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';

    $check_offbeat = $pdo->prepare("SELECT * FROM tour_categories WHERE name = ? OR name = 'Offbeat'");
    $check_offbeat->execute([$offbeat_name]);
    if (!$check_offbeat->fetch()) {
        $insert = $pdo->prepare("INSERT INTO tour_categories (name, tagline, image_url, icon_svg, display_order, is_active) VALUES (?, ?, ?, ?, ?, 1)");
        $insert->execute([$offbeat_name, $offbeat_tagline, $offbeat_bg, $offbeat_icon, 11]);
        echo "<p style='color: #4ade80;'>✔ Inserted 'Off Beat' into tour_categories table.</p>";
    } else {
        echo "<p style='color: #94a3b8;'>✔ 'Off Beat' already exists in tour_categories.</p>";
    }

    $stmt = $pdo->query("SELECT id, tour_type FROM packages WHERE is_active = 1 ORDER BY id DESC LIMIT 2");
    $sample_pkgs = $stmt->fetchAll();
    foreach ($sample_pkgs as $pkg) {
        $types = !empty($pkg['tour_type']) ? array_map('trim', explode(',', $pkg['tour_type'])) : [];
        if (!in_array('Off Beat', $types) && !in_array('Offbeat', $types)) {
            $types[] = 'Off Beat';
            $new_type = implode(', ', $types);
            $update = $pdo->prepare("UPDATE packages SET tour_type = ? WHERE id = ?");
            $update->execute([$new_type, $pkg['id']]);
            echo "<p style='color: #4ade80;'>✔ Assigned 'Off Beat' theme to package ID {$pkg['id']}.</p>";
        }
    }

    echo "<h3 style='color: #4ade80; margin-top: 30px;'>🎉 Database successfully updated!</h3>";
    echo "<p style='margin-top: 15px;'><a href='index.php' style='display: inline-block; padding: 10px 20px; background: #c5a059; color: #0b1329; text-decoration: none; font-weight: bold; border-radius: 5px;'>← Go to Homepage & see changes</a></p>";
} catch (Exception $e) {
    echo "<p style='color: #f87171;'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
}
echo "</div>";
