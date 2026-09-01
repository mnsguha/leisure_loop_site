<?php
require_once '../config/db.php';

try {
    // Check if 'Family' exists in tour_categories
    $stmt = $pdo->prepare("SELECT * FROM tour_categories WHERE name = 'Family'");
    $stmt->execute();
    $family_cat = $stmt->fetch();

    if (!$family_cat) {
        $icon_svg = '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>';
        $bg_url = 'https://images.unsplash.com/photo-1600880292203-757bb62b4baf?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80'; // Random nice family/travel photo
        
        $insert = $pdo->prepare("INSERT INTO tour_categories (name, tagline, image_url, icon_svg, display_order, is_active) VALUES (?, ?, ?, ?, ?, ?)");
        $insert->execute(['Family', 'Create memories with your loved ones', $bg_url, $icon_svg, 10, 1]);
        echo "Inserted 'Family' into tour_categories.\n";
    } else {
        echo "'Family' already exists in tour_categories.\n";
    }

    // Assign 'Family' to at least one package so it shows up
    $stmt = $pdo->query("SELECT id, tour_type FROM packages WHERE is_active = 1 LIMIT 2");
    $packages = $stmt->fetchAll();
    
    foreach ($packages as $pkg) {
        $types = explode(',', $pkg['tour_type']);
        $types = array_map('trim', $types);
        if (!in_array('Family', $types)) {
            $types[] = 'Family';
            $new_type = implode(', ', $types);
            $update = $pdo->prepare("UPDATE packages SET tour_type = ? WHERE id = ?");
            $update->execute([$new_type, $pkg['id']]);
            echo "Updated package ID {$pkg['id']} with 'Family' theme.\n";
        }
    }

    echo "Done.\n";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
