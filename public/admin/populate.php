<?php
require_once '../../config/db.php';

$sikkim_parallax = [
    [
        'image' => 'images/parallax/sunset_sky.png',
        'class' => 'p-layer p-layer-sky',
        'style' => 'position: absolute; inset: -10%; width: 120%; height: 120%; object-fit: cover; z-index: 1; pointer-events: none;',
        'z_index' => '1',
        'depth' => '0.05'
    ],
    [
        'image' => 'images/parallax/sunset_mountains.png',
        'class' => 'p-layer p-layer-back',
        'style' => 'position: absolute; left: -5%; bottom: -10%; width: 110%; height: 120%; object-fit: cover; z-index: 2; pointer-events: none;',
        'z_index' => '2',
        'depth' => '0.15'
    ],
    [
        'image' => 'images/parallax/sunset_village.png',
        'class' => 'p-layer p-layer-mid',
        'style' => 'position: absolute; left: -5%; bottom: -15%; width: 110%; height: 125%; object-fit: cover; z-index: 4; pointer-events: none; transform-origin: center bottom;',
        'z_index' => '4',
        'depth' => '0.4'
    ],
    [
        'image' => 'images/parallax/custom_cloud_1.png',
        'class' => 'cloud-img-1',
        'style' => 'position: absolute; inset: -10%; width: 120%; height: 120%; z-index: 5; pointer-events: none;',
        'z_index' => '5',
        'depth' => '0.5'
    ],
    [
        'image' => 'images/parallax/custom_cloud_3.png',
        'class' => 'cloud-img-3',
        'style' => 'position: absolute; inset: -10%; width: 120%; height: 120%; z-index: 5; pointer-events: none;',
        'z_index' => '5',
        'depth' => '0.6'
    ],
    [
        'image' => 'images/parallax/custom_cloud_2.png',
        'class' => 'cloud-img-2',
        'style' => 'position: absolute; inset: -10%; width: 120%; height: 120%; z-index: 6; pointer-events: none;',
        'z_index' => '6',
        'depth' => '0.7'
    ],
    [
        'image' => 'images/parallax/sunset_flags.png',
        'class' => 'p-layer p-layer-foremost',
        'style' => 'position: absolute; left: -10%; bottom: -30%; width: 120%; height: 150%; object-fit: cover; z-index: 7; pointer-events: none; transform-origin: center bottom;',
        'z_index' => '7',
        'depth' => '0.9'
    ]
];

$darj_parallax = [
    [
        'image' => 'images/parallax/darj_sky.png',
        'class' => 'p-layer p-layer-sky',
        'style' => 'position: absolute; inset: -5%; width: 110%; height: 110%; object-fit: cover; z-index: 1; pointer-events: none;',
        'z_index' => '1',
        'depth' => '0.05'
    ],
    [
        'image' => 'images/parallax/darj_snow_mountain.png',
        'class' => 'p-layer p-layer-back',
        'style' => 'position: absolute; inset: -5%; width: 110%; height: 110%; object-fit: cover; z-index: 2; pointer-events: none;',
        'z_index' => '2',
        'depth' => '0.10'
    ],
    [
        'image' => 'images/parallax/darj_distant_mountain.png',
        'class' => 'p-layer p-layer-mid',
        'style' => 'position: absolute; inset: -5%; width: 110%; height: 110%; object-fit: cover; z-index: 4; pointer-events: none;',
        'z_index' => '4',
        'depth' => '0.25'
    ],
    [
        'image' => 'images/parallax/custom_cloud_1.png',
        'class' => 'cloud-img-1',
        'style' => 'position: absolute; inset: -10%; width: 120%; height: 120%; z-index: 4; pointer-events: none; bottom: 45%;',
        'z_index' => '4',
        'depth' => '0.5'
    ],
    [
        'image' => 'images/parallax/custom_cloud_3.png',
        'class' => 'cloud-img-3',
        'style' => 'position: absolute; inset: -10%; width: 120%; height: 120%; z-index: 4; pointer-events: none; bottom: 50%;',
        'z_index' => '4',
        'depth' => '0.6'
    ],
    [
        'image' => 'images/parallax/custom_cloud_2.png',
        'class' => 'cloud-img-2',
        'style' => 'position: absolute; inset: -10%; width: 120%; height: 120%; z-index: 4; pointer-events: none; bottom: 38%;',
        'z_index' => '4',
        'depth' => '0.7'
    ],
    [
        'image' => 'images/parallax/darj_teagarden.png',
        'class' => 'p-layer p-layer-foremost',
        'style' => 'position: absolute; left: -10%; bottom: -30%; width: 120%; height: 150%; object-fit: cover; z-index: 7; pointer-events: none; transform-origin: center bottom;',
        'z_index' => '7',
        'depth' => '0.40'
    ]
];

$local_exp = [
    [
        'title' => 'Private Tea Tasting',
        'icon' => 'local_cafe',
        'desc' => 'Discover the heritage of Temi Tea Garden with a master sommelier.'
    ],
    [
        'title' => 'Monastic Meditation',
        'icon' => 'self_improvement',
        'desc' => 'Join senior monks for an exclusive dawn meditation session.'
    ],
    [
        'title' => 'Alpine Helicopter Tour',
        'icon' => 'flight_takeoff',
        'desc' => 'Soar above the clouds with panoramic views of Kanchenjunga.'
    ]
];

try {
    $pdo->prepare("UPDATE destinations SET parallax_layers_json = ?, local_experiences_json = ?, altitude = '3500m+', best_time = 'Oct-May', duration = '7-10 Days', story_narrative_image_2 = 'images/stitch/stitch_img_3.jpg' WHERE slug = 'sikkim'")->execute([json_encode($sikkim_parallax), json_encode($local_exp)]);
    
    $pdo->prepare("UPDATE destinations SET parallax_layers_json = ?, local_experiences_json = ?, altitude = '2000m+', best_time = 'Mar-Jun', duration = '4-6 Days', story_narrative_image_2 = 'images/stitch/stitch_img_3.jpg' WHERE slug = 'darjeeling'")->execute([json_encode($darj_parallax), json_encode($local_exp)]);
    
    echo "POPULATED";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage();
}
