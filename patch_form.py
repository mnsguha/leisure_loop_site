import re

filepath = 'public/admin/destination-form.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add hero_video_url to defaults
content = content.replace("'cover_image' => '',", "'cover_image' => '',\n    'hero_video_url' => '',")

# 2. Add hero_video_url to POST vars
content = content.replace("$cover_image = trim($_POST['cover_image'] ?? '');", "$cover_image = trim($_POST['cover_image'] ?? '');\n    $hero_video_url = trim($_POST['hero_video_url'] ?? '');")

# 3. Remove parallax_layers POST parsing
parallax_parse = """    $parallax_data = [];
    if (isset($_POST['p_img']) && is_array($_POST['p_img'])) {
        foreach ($_POST['p_img'] as $key => $val) {
            $pImg = trim((string) $val);
            if ($pImg === '') continue;
            $parallax_data[] = [
                'image' => $pImg,
                'class' => trim((string) ($_POST['p_class'][$key] ?? '')),
                'style' => trim((string) ($_POST['p_style'][$key] ?? '')),
                'z_index' => trim((string) ($_POST['p_zindex'][$key] ?? '')),
                'depth' => trim((string) ($_POST['p_depth'][$key] ?? ''))
            ];
        }
    }
    $parallax_layers_json = json_encode($parallax_data);"""
content = content.replace(parallax_parse, "")

# 4. Update the SQL queries
old_update = '"UPDATE destinations SET name=?, slug=?, tagline=?, category=?, altitude=?, best_time=?, duration=?, cover_image=?, story_narrative_image=?, story_narrative_image_2=?, story_sightseeing_image=?, story_packages_image=?, description_long=?, display_order=?, is_active=?, sightseeing_json=?, parallax_layers_json=?, local_experiences_json=?, terms_conditions=? WHERE id=?"'
new_update = '"UPDATE destinations SET name=?, slug=?, tagline=?, category=?, altitude=?, best_time=?, duration=?, cover_image=?, hero_video_url=?, story_narrative_image=?, story_narrative_image_2=?, story_sightseeing_image=?, story_packages_image=?, description_long=?, display_order=?, is_active=?, sightseeing_json=?, local_experiences_json=?, terms_conditions=? WHERE id=?"'
content = content.replace(old_update, new_update)

old_update_params = '[$name, $slug, $tagline, $category, $altitude, $best_time, $duration, $cover_image, $story_narrative_image, $story_narrative_image_2, $story_sightseeing_image, $story_packages_image, $description_long, $display_order, $is_active, $sightseeing_json, $parallax_layers_json, $local_experiences_json, $terms_conditions, $id]'
new_update_params = '[$name, $slug, $tagline, $category, $altitude, $best_time, $duration, $cover_image, $hero_video_url, $story_narrative_image, $story_narrative_image_2, $story_sightseeing_image, $story_packages_image, $description_long, $display_order, $is_active, $sightseeing_json, $local_experiences_json, $terms_conditions, $id]'
content = content.replace(old_update_params, new_update_params)

old_insert = '"INSERT INTO destinations (name, slug, tagline, category, altitude, best_time, duration, cover_image, story_narrative_image, story_narrative_image_2, story_sightseeing_image, story_packages_image, description_long, display_order, is_active, sightseeing_json, parallax_layers_json, local_experiences_json, terms_conditions) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"'
new_insert = '"INSERT INTO destinations (name, slug, tagline, category, altitude, best_time, duration, cover_image, hero_video_url, story_narrative_image, story_narrative_image_2, story_sightseeing_image, story_packages_image, description_long, display_order, is_active, sightseeing_json, parallax_layers_json, local_experiences_json, terms_conditions) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"'
content = content.replace(old_insert, new_insert)

old_insert_params = '[$name, $slug, $tagline, $category, $altitude, $best_time, $duration, $cover_image, $story_narrative_image, $story_narrative_image_2, $story_sightseeing_image, $story_packages_image, $description_long, $display_order, $is_active, $sightseeing_json, $parallax_layers_json, $local_experiences_json, $terms_conditions]'
# Set parallax_layers_json to default empty json '[]' for new ones
new_insert_params = '[$name, $slug, $tagline, $category, $altitude, $best_time, $duration, $cover_image, $hero_video_url, $story_narrative_image, $story_narrative_image_2, $story_sightseeing_image, $story_packages_image, $description_long, $display_order, $is_active, $sightseeing_json, "[]", $local_experiences_json, $terms_conditions]'
content = content.replace(old_insert_params, new_insert_params)


# 5. Remove HTML for parallax
parallax_html = """                <div style="margin-top: 3rem; margin-bottom: 2rem;">
                    <h3 style="margin-bottom: 1rem; color: var(--gold);">Parallax Hero Layers</h3>
                    <p style="color: var(--on-surface-variant); margin-bottom: 1rem; font-size: 0.9rem;">Add background image layers from back to front. The Parallax builder replaces the default static Hero block on the destination details page.</p>
                    <div id="parallax-container">
                        <?php foreach ($dest['parallax_layers'] as $index => $layer): ?>
                        <div class="spot-item">
                            <span class="btn-remove" onclick="this.parentElement.remove()">Remove</span>
                            <div class="form-group">
                                <label>Image URL</label>
                                <input type="text" name="p_img[]" value="<?php echo htmlspecialchars($layer['image'] ?? ''); ?>" required placeholder="images/parallax/my_layer.png">
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>CSS Class (e.g. p-layer-back)</label>
                                    <input type="text" name="p_class[]" value="<?php echo htmlspecialchars($layer['class'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Inline Style (e.g. inset: -5%; width: 110%;)</label>
                                    <input type="text" name="p_style[]" value="<?php echo htmlspecialchars($layer['style'] ?? ''); ?>">
                                </div>
                            </div>
                            <div class="form-row">
                                <div class="form-group">
                                    <label>Z-Index (e.g. 1)</label>
                                    <input type="text" name="p_zindex[]" value="<?php echo htmlspecialchars($layer['z_index'] ?? ''); ?>">
                                </div>
                                <div class="form-group">
                                    <label>Data-Depth (e.g. 0.15)</label>
                                    <input type="text" name="p_depth[]" value="<?php echo htmlspecialchars($layer['depth'] ?? ''); ?>">
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" id="add-parallax" class="btn-outline" style="width: 100%;">+ Add Parallax Layer</button>
                </div>"""
content = content.replace(parallax_html, "")

# 6. Add HTML for hero_video_url next to cover_image
cover_html = """                <div class="form-group">
                    <label>Cover/Fallback Hero Image URL</label>
                    <input type="text" name="cover_image" value="<?php echo htmlspecialchars($dest['cover_image']); ?>" placeholder="https://...">
                </div>"""
hero_video_html = """                <div class="form-row">
                    <div class="form-group">
                        <label>Cover/Fallback Hero Image URL</label>
                        <input type="text" name="cover_image" value="<?php echo htmlspecialchars($dest['cover_image']); ?>" placeholder="https://...">
                    </div>
                    <div class="form-group">
                        <label>Hero Video URL (.mp4)</label>
                        <input type="text" name="hero_video_url" value="<?php echo htmlspecialchars($dest['hero_video_url'] ?? ''); ?>" placeholder="https://...">
                    </div>
                </div>"""
content = content.replace(cover_html, hero_video_html)

# 7. Remove JS for parallax
parallax_js = """        document.getElementById('add-parallax').addEventListener('click', () => {
            const container = document.getElementById('parallax-container');
            const div = document.createElement('div');
            div.className = 'spot-item';
            div.innerHTML = `
                <span class="btn-remove" onclick="this.parentElement.remove()">Remove</span>
                <div class="form-group">
                    <label>Image URL</label>
                    <input type="text" name="p_img[]" required placeholder="images/parallax/my_layer.png">
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>CSS Class (e.g. p-layer-back)</label>
                        <input type="text" name="p_class[]">
                    </div>
                    <div class="form-group">
                        <label>Inline Style (e.g. inset: -5%; width: 110%;)</label>
                        <input type="text" name="p_style[]">
                    </div>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label>Z-Index (e.g. 1)</label>
                        <input type="text" name="p_zindex[]">
                    </div>
                    <div class="form-group">
                        <label>Data-Depth (e.g. 0.15)</label>
                        <input type="text" name="p_depth[]">
                    </div>
                </div>
            `;
            container.appendChild(div);
        });"""
content = content.replace(parallax_js, "")


with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated destination-form.php")
