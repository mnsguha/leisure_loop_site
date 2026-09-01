<?php
require_once __DIR__ . '/../config/db.php';

if (!$pdo) {
    echo "PDO is null from config/db.php. Attempting direct connection with debugging...\n";
    try {
        $pdo = new PDO("mysql:host=127.0.0.1;dbname=leisure_loop_db;charset=utf8mb4", "root", "", [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]);
    } catch (Exception $e) {
        echo "Direct connection error: " . $e->getMessage() . "\n";
        try {
            $pdo = new PDO("mysql:host=localhost:3306;dbname=leisure_loop_db;charset=utf8mb4", "root", "", [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        } catch (Exception $e2) {
            die("Could not connect to database: " . $e2->getMessage() . "\n");
        }
    }
}

try {
    echo "--- 1. Replacing 'Group' theme with 'Beach' ---\n";
    
    // Check if 'Group' exists in tour_categories
    $stmt = $pdo->prepare("SELECT * FROM tour_categories WHERE name = 'Group'");
    $stmt->execute();
    $group_cat = $stmt->fetch();

    $beach_icon = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 12c-2 0-3-1-4.5-1S10 12 8 12s-3-1-4.5-1S1 12 1 12v6c0 1.5 2 2 3.5 2s2.5-1 4-1 2.5 1 4 1 2.5-1 4-1 2.5 1 3.5 1v-6c0 0-1-1-2.5-1s-2.5 1-4.5 1z"/><circle cx="12" cy="6" r="3"/><path d="M2 22h20"/></svg>';
    $beach_bg = 'https://images.unsplash.com/photo-1507525428034-b723cf961d3e?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';
    $beach_tagline = 'Pristine coastal bliss & tropical serenity';

    if ($group_cat) {
        // Update 'Group' to 'Beach'
        $update_cat = $pdo->prepare("UPDATE tour_categories SET name = 'Beach', tagline = ?, image_url = ?, icon_svg = ? WHERE name = 'Group'");
        $update_cat->execute([$beach_tagline, $beach_bg, $beach_icon]);
        echo "Successfully updated 'Group' category to 'Beach' in tour_categories.\n";
    } else {
        // If Beach does not exist, insert it
        $check_beach = $pdo->prepare("SELECT * FROM tour_categories WHERE name = 'Beach'");
        $check_beach->execute();
        if (!$check_beach->fetch()) {
            $insert = $pdo->prepare("INSERT INTO tour_categories (name, tagline, image_url, icon_svg, display_order, is_active) VALUES (?, ?, ?, ?, ?, 1)");
            $insert->execute(['Beach', $beach_tagline, $beach_bg, $beach_icon, 5]);
            echo "Inserted 'Beach' into tour_categories.\n";
        } else {
            echo "'Beach' already exists in tour_categories.\n";
        }
    }

    // Update all packages that currently have 'Group' in tour_type to have 'Beach' instead
    $pkgs = $pdo->query("SELECT id, tour_type FROM packages WHERE is_active = 1")->fetchAll();
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
                echo "Updated package ID {$pkg['id']}: replaced 'Group' with 'Beach'.\n";
            }
        }
    }

    echo "\n--- 2. Adding 'Off Beat' theme ---\n";

    $offbeat_name = 'Off Beat';
    $offbeat_tagline = 'Uncharted wilderness & secret local escapes';
    $offbeat_icon = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polygon points="16.24 7.76 14.12 14.12 7.76 16.24 9.88 9.88 16.24 7.76"/></svg>';
    $offbeat_bg = 'https://images.unsplash.com/photo-1470071459604-3b5ec3a7fe05?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';

    $check_offbeat = $pdo->prepare("SELECT * FROM tour_categories WHERE name = ? OR name = 'Offbeat'");
    $check_offbeat->execute([$offbeat_name]);
    if (!$check_offbeat->fetch()) {
        $insert = $pdo->prepare("INSERT INTO tour_categories (name, tagline, image_url, icon_svg, display_order, is_active) VALUES (?, ?, ?, ?, ?, 1)");
        $insert->execute([$offbeat_name, $offbeat_tagline, $offbeat_bg, $offbeat_icon, 11]);
        echo "Inserted 'Off Beat' into tour_categories.\n";
    } else {
        echo "'Off Beat' already exists in tour_categories.\n";
    }

    // Assign 'Off Beat' to at least one active package so it immediately renders in the UI
    $stmt = $pdo->query("SELECT id, tour_type FROM packages WHERE is_active = 1 ORDER BY RAND() LIMIT 2");
    $sample_pkgs = $stmt->fetchAll();
    foreach ($sample_pkgs as $pkg) {
        $types = !empty($pkg['tour_type']) ? array_map('trim', explode(',', $pkg['tour_type'])) : [];
        if (!in_array('Off Beat', $types) && !in_array('Offbeat', $types)) {
            $types[] = 'Off Beat';
            $new_type = implode(', ', $types);
            $update = $pdo->prepare("UPDATE packages SET tour_type = ? WHERE id = ?");
            $update->execute([$new_type, $pkg['id']]);
            echo "Assigned 'Off Beat' theme to package ID {$pkg['id']}.\n";
        }
    }

    echo "\nAll themes updated successfully!\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
