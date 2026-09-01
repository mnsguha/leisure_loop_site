<?php
require_once '../../includes/functions.php';
require_once '../../config/db.php';
requireAdmin();

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$dest = [
    'name' => '',
    'slug' => '',
    'tagline' => '',
    'category' => 'domestic',
    'altitude' => '',
    'best_time' => '',
    'duration' => '',
    'cover_image' => '',
    'card_image' => '',
    'hero_video_url' => '',
    'story_narrative_image' => '',
    'story_narrative_image_2' => '',
    'story_narrative_image_3' => '',
    'narrative_watermark' => '',
    'story_sightseeing_image' => '',
    'story_packages_image' => '',
    'description_long' => '',
    'display_order' => 0,
    'is_active' => 1,
    'sightseeing' => [],
    'parallax_layers' => [],
    'local_experiences' => [],
    'terms_conditions' => ''
];

if ($id > 0 && $pdo) {
    $stmt = $pdo->prepare("SELECT * FROM destinations WHERE id = ?");
    $stmt->execute([$id]);
    $res = $stmt->fetch();
    if ($res) {
        $dest = array_merge($dest, $res);
        $dest['sightseeing'] = json_decode($res['sightseeing_json'], true) ?: [];
        $dest['parallax_layers'] = json_decode($res['parallax_layers_json'], true) ?: [];
        $dest['local_experiences'] = json_decode($res['local_experiences_json'], true) ?: [];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $pdo) {
    $name = trim($_POST['name'] ?? '');
    $slug = trim($_POST['slug'] ?? '') ?: slugify($name);
    $tagline = trim($_POST['tagline'] ?? '');
    $category = trim($_POST['category'] ?? 'domestic');
    $altitude = trim($_POST['altitude'] ?? '');
    $best_time = trim($_POST['best_time'] ?? '');
    $duration = trim($_POST['duration'] ?? '');
    $cover_image = trim($_POST['cover_image'] ?? '');
    $card_image = trim($_POST['card_image'] ?? '');
    $hero_video_url = trim($_POST['hero_video_url'] ?? '');
    $story_narrative_image = trim($_POST['story_narrative_image'] ?? '');
    $story_narrative_image_2 = trim($_POST['story_narrative_image_2'] ?? '');
    $story_narrative_image_3 = trim($_POST['story_narrative_image_3'] ?? '');
    $narrative_watermark = trim($_POST['narrative_watermark'] ?? '');
    $story_sightseeing_image = trim($_POST['story_sightseeing_image'] ?? '');
    $story_packages_image = trim($_POST['story_packages_image'] ?? '');
    
    // Process single image uploads
    $uploadDir = __DIR__ . '/../images/dest/';
    $publicPath = 'images/dest/';
    
    $uploadedCover = handleImageUpload('upload_cover_image', $uploadDir, $publicPath);
    if ($uploadedCover) $cover_image = $uploadedCover;
    
    $uploadedCard = handleImageUpload('upload_card_image', $uploadDir, $publicPath);
    if ($uploadedCard) $card_image = $uploadedCard;
    
    $uploadedNarrative = handleImageUpload('upload_story_narrative_image', $uploadDir, $publicPath);
    if ($uploadedNarrative) $story_narrative_image = $uploadedNarrative;
    
    $uploadedNarrative2 = handleImageUpload('upload_story_narrative_image_2', $uploadDir, $publicPath);
    if ($uploadedNarrative2) $story_narrative_image_2 = $uploadedNarrative2;
    
    $uploadedNarrative3 = handleImageUpload('upload_story_narrative_image_3', $uploadDir, $publicPath);
    if ($uploadedNarrative3) $story_narrative_image_3 = $uploadedNarrative3;

    $description_long = trim($_POST['description_long'] ?? '');
    $display_order = (int)($_POST['display_order'] ?? 0);
    $is_active = isset($_POST['is_active']) ? 1 : 0;
    $terms_conditions = trim($_POST['terms_conditions'] ?? '');

    $sightseeing_data = [];
    if (isset($_POST['spot_title']) && is_array($_POST['spot_title'])) {
        foreach ($_POST['spot_title'] as $key => $val) {
            $spotTitle = trim((string) $val);
            $spotDesc = trim((string) ($_POST['spot_desc'][$key] ?? ''));
            $spotImage = trim((string) ($_POST['spot_image'][$key] ?? ''));
            
            $uploadedSpotImg = handleArrayImageUpload('upload_spot_image', $key, $uploadDir, $publicPath);
            if ($uploadedSpotImg) {
                $spotImage = $uploadedSpotImg;
            }
            
            if ($spotTitle === '' && $spotDesc === '') continue;

            $sightseeing_data[] = [
                'title' => $spotTitle,
                'desc' => $spotDesc,
                'image' => $spotImage
            ];
        }
    }
    $sightseeing_json = json_encode($sightseeing_data);



    $experiences_data = [];
    if (isset($_POST['exp_title']) && is_array($_POST['exp_title'])) {
        foreach ($_POST['exp_title'] as $key => $val) {
            $eTitle = trim((string) $val);
            if ($eTitle === '') continue;
            $experiences_data[] = [
                'title' => $eTitle,
                'icon' => trim((string) ($_POST['exp_icon'][$key] ?? '')),
                'desc' => trim((string) ($_POST['exp_desc'][$key] ?? ''))
            ];
        }
    }
    $local_experiences_json = json_encode($experiences_data);

    if ($id > 0) {
        $stmt = $pdo->prepare("UPDATE destinations SET name=?, slug=?, tagline=?, category=?, altitude=?, best_time=?, duration=?, cover_image=?, card_image=?, hero_video_url=?, story_narrative_image=?, story_narrative_image_2=?, story_narrative_image_3=?, narrative_watermark=?, story_sightseeing_image=?, story_packages_image=?, description_long=?, display_order=?, is_active=?, sightseeing_json=?, local_experiences_json=?, terms_conditions=? WHERE id=?");
        $stmt->execute([$name, $slug, $tagline, $category, $altitude, $best_time, $duration, $cover_image, $card_image, $hero_video_url, $story_narrative_image, $story_narrative_image_2, $story_narrative_image_3, $narrative_watermark, $story_sightseeing_image, $story_packages_image, $description_long, $display_order, $is_active, $sightseeing_json, $local_experiences_json, $terms_conditions, $id]);
    } else {
        $stmt = $pdo->prepare("INSERT INTO destinations (name, slug, tagline, category, altitude, best_time, duration, cover_image, card_image, hero_video_url, story_narrative_image, story_narrative_image_2, story_narrative_image_3, narrative_watermark, story_sightseeing_image, story_packages_image, description_long, display_order, is_active, sightseeing_json, parallax_layers_json, local_experiences_json, terms_conditions) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([$name, $slug, $tagline, $category, $altitude, $best_time, $duration, $cover_image, $card_image, $hero_video_url, $story_narrative_image, $story_narrative_image_2, $story_narrative_image_3, $narrative_watermark, $story_sightseeing_image, $story_packages_image, $description_long, $display_order, $is_active, $sightseeing_json, "[]", $local_experiences_json, $terms_conditions]);
    }

    header('Location: destinations.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $id ? 'Edit' : 'Add'; ?> Destination | Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="../css/style.css">
    <link rel="stylesheet" href="../css/admin.css?v=2">
</head>
<body>
    <div class="admin-layout">
        <?php include 'sidebar.php'; ?>

        <main class="main-content">
            <div class="header" style="margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1);">
                <div style="display: flex; align-items: center; gap: 15px;">
                    <a href="destinations.php" style="color:var(--gold); font-size: 1.8rem; text-decoration: none; line-height: 1;" title="Back to Destinations">←</a>
                    <h1 style="margin: 0;"><?php echo $id ? 'Edit' : 'Create'; ?> <span class="accent">Destination</span></h1>
                </div>
            </div>

            <form method="POST" enctype="multipart/form-data" class="form-card">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                <div class="form-row">
                    <div class="form-group">
                        <label>Destination Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($dest['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Slug (URL Identifier)</label>
                        <input type="text" name="slug" value="<?php echo htmlspecialchars($dest['slug']); ?>" placeholder="auto-generated if empty">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Tagline (e.g. Himalayan Majesty)</label>
                        <input type="text" name="tagline" value="<?php echo htmlspecialchars($dest['tagline']); ?>">
                    </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category">
                            <option value="domestic" <?php echo $dest['category'] == 'domestic' ? 'selected' : ''; ?>>Domestic</option>
                            <option value="international" <?php echo $dest['category'] == 'international' ? 'selected' : ''; ?>>International</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Display Order (Lower = First)</label>
                        <input type="number" name="display_order" value="<?php echo (int)$dest['display_order']; ?>">
                    </div>
                </div>

                <h3 style="margin-top: 1rem; margin-bottom: 1rem; color: var(--gold);">Quick Facts</h3>
                <div class="form-row">
                    <div class="form-group">
                        <label>Altitude (e.g. 3500m+)</label>
                        <input type="text" name="altitude" value="<?php echo htmlspecialchars($dest['altitude']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Best Time (e.g. Oct-May)</label>
                        <input type="text" name="best_time" value="<?php echo htmlspecialchars($dest['best_time']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Duration (e.g. 7-10 Days)</label>
                        <input type="text" name="duration" value="<?php echo htmlspecialchars($dest['duration']); ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Cover/Fallback Hero Image URL</label>
                        <input type="text" name="cover_image" value="<?php echo htmlspecialchars($dest['cover_image']); ?>" placeholder="https://..." style="margin-bottom: 0.5rem;">
                        <input type="file" name="upload_cover_image" accept="image/*">
                        <small style="color: #64748b;">Or upload a new file.</small>
                    </div>
                    <div class="form-group">
                        <label>Destination Card Image URL (Portrait)</label>
                        <input type="text" name="card_image" value="<?php echo htmlspecialchars($dest['card_image'] ?? ''); ?>" placeholder="https://..." style="margin-bottom: 0.5rem;">
                        <input type="file" name="upload_card_image" accept="image/*">
                        <small style="color: #64748b;">Or upload a new file.</small>
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Hero Video URL (.mp4)</label>
                        <input type="text" name="hero_video_url" value="<?php echo htmlspecialchars($dest['hero_video_url'] ?? ''); ?>" placeholder="https://...">
                    </div>
                </div>
                
                <h3 style="margin-top: 1rem; margin-bottom: 1rem; color: var(--gold);">Storytelling Parallax Images</h3>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Narrative Section Image URL 1</label>
                        <input type="text" name="story_narrative_image" value="<?php echo htmlspecialchars($dest['story_narrative_image'] ?? ''); ?>" placeholder="Fallback to Hero Image if empty" style="margin-bottom: 0.5rem;">
                        <input type="file" name="upload_story_narrative_image" accept="image/*">
                    </div>
                    <div class="form-group">
                        <label>Narrative Section Image URL 2</label>
                        <input type="text" name="story_narrative_image_2" value="<?php echo htmlspecialchars($dest['story_narrative_image_2'] ?? ''); ?>" placeholder="Secondary stacked image" style="margin-bottom: 0.5rem;">
                        <input type="file" name="upload_story_narrative_image_2" accept="image/*">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Narrative Section Image URL 3</label>
                        <input type="text" name="story_narrative_image_3" value="<?php echo htmlspecialchars($dest['story_narrative_image_3'] ?? ''); ?>" placeholder="Tertiary stacked image" style="margin-bottom: 0.5rem;">
                        <input type="file" name="upload_story_narrative_image_3" accept="image/*">
                    </div>
                </div>
                


                <div class="form-group">
                    <label>Editorial Description (Long Text)</label>
                    <textarea name="description_long" rows="6"><?php echo htmlspecialchars($dest['description_long']); ?></textarea>
                </div>

                <div class="form-group" style="margin-top: 1.5rem;">
                    <label>Terms &amp; Conditions <span style="color:#64748b;font-weight:400;font-size:0.8rem;">&#8212; shown on the package detail page. Use ##text## to highlight a word or sentence in gold.</span></label>
                    
<label for="input_e01f7572" class="sr-only">##Terms &amp; Conditions##&#10;&#10;##Payment Policy##&#10;An advance payment of 30% of the total tour cost + 5% GST...</label>
<textarea id="input_e01f7572" name="terms_conditions" rows="8" placeholder="##Terms &amp; Conditions##&#10;&#10;##Payment Policy##&#10;An advance payment of 30% of the total tour cost + 5% GST..."><?php echo htmlspecialchars($dest['terms_conditions'] ?? ''); ?></textarea>
                </div>

                <div class="form-group">
                    <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                        <input type="checkbox" name="is_active" <?php echo $dest['is_active'] ? 'checked' : ''; ?> style="width: auto;">
                        Active (published on website)
                    </label>
                </div>

                <div style="margin-top: 3rem; margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1rem;">Sightseeing Highlights</h3>
                    <div id="spots-container">
                        <?php foreach ($dest['sightseeing'] as $index => $spot): ?>
                        <div class="spot-item">
                            <span class="btn-remove" data-action="remove-parent">Remove</span>
                            <div class="form-group">
                                <label>Spot Title</label>
                                <input type="text" name="spot_title[]" value="<?php echo htmlspecialchars($spot['title'] ?? ''); ?>" required>
                            </div>
                            <div class="form-group">
                                <label>Image URL</label>
                                <input type="text" name="spot_image[]" value="<?php echo htmlspecialchars($spot['image'] ?? ''); ?>" style="margin-bottom: 0.5rem;">
                                <input type="file" name="upload_spot_image[]" accept="image/*">
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="spot_desc[]" rows="2"><?php echo htmlspecialchars($spot['desc'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" id="add-spot" class="btn-outline" style="width: 100%;">+ Add Sightseeing Spot</button>
                </div>

                <div style="margin-top: 3rem; margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1rem; color: var(--gold);">Local Experiences</h3>
                    <div id="experiences-container">
                        <?php foreach ($dest['local_experiences'] as $index => $exp): ?>
                        <div class="spot-item">
                            <span class="btn-remove" data-action="remove-parent">Remove</span>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Title</label>
                                    <input type="text" name="exp_title[]" value="<?php echo htmlspecialchars($exp['title'] ?? ''); ?>" required>
                                </div>
                                <div class="form-group">
                                    <label>Material Icon Name (e.g. local_cafe)</label>
                                    <input type="text" name="exp_icon[]" value="<?php echo htmlspecialchars($exp['icon'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Description</label>
                                <textarea name="exp_desc[]" rows="2"><?php echo htmlspecialchars($exp['desc'] ?? ''); ?></textarea>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" id="add-exp" class="btn-outline" style="width: 100%;">+ Add Local Experience</button>
                </div>



                <button type="submit" class="btn-primary" style="width: 100%; padding: 1.25rem;">Save Destination</button>
            </form>
        </main>
    </div>

    <script src="../js/modules/admin-scripts.js" defer></script>
</body>
</html>
