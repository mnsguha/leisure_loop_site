<?php
require_once __DIR__ . '/../config/db.php';
$stmt = $pdo->prepare("SELECT itinerary, map_coords, use_destination_terms, terms_conditions, dest_terms FROM packages p LEFT JOIN destinations d ON p.destination = d.name WHERE slug = 'meghalaya-living-roots'");
$stmt->execute();
print_r($stmt->fetch(PDO::FETCH_ASSOC));
