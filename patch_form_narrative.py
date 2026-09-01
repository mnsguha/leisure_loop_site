import re

filepath = 'public/admin/destination-form.php'
with open(filepath, 'r', encoding='utf-8') as f:
    content = f.read()

# 1. Add story_narrative_image_3 to defaults
content = content.replace("'story_narrative_image_2' => '',", "'story_narrative_image_2' => '',\n    'story_narrative_image_3' => '',")

# 2. Add story_narrative_image_3 to POST vars
content = content.replace("$story_narrative_image_2 = trim($_POST['story_narrative_image_2'] ?? '');", "$story_narrative_image_2 = trim($_POST['story_narrative_image_2'] ?? '');\n    $story_narrative_image_3 = trim($_POST['story_narrative_image_3'] ?? '');")

# 3. Update the SQL UPDATE queries
old_update = '"UPDATE destinations SET name=?, slug=?, tagline=?, category=?, altitude=?, best_time=?, duration=?, cover_image=?, hero_video_url=?, story_narrative_image=?, story_narrative_image_2=?, story_sightseeing_image=?, story_packages_image=?, description_long=?, display_order=?, is_active=?, sightseeing_json=?, local_experiences_json=?, terms_conditions=? WHERE id=?"'
new_update = '"UPDATE destinations SET name=?, slug=?, tagline=?, category=?, altitude=?, best_time=?, duration=?, cover_image=?, hero_video_url=?, story_narrative_image=?, story_narrative_image_2=?, story_narrative_image_3=?, story_sightseeing_image=?, story_packages_image=?, description_long=?, display_order=?, is_active=?, sightseeing_json=?, local_experiences_json=?, terms_conditions=? WHERE id=?"'
content = content.replace(old_update, new_update)

old_update_params = '[$name, $slug, $tagline, $category, $altitude, $best_time, $duration, $cover_image, $hero_video_url, $story_narrative_image, $story_narrative_image_2, $story_sightseeing_image, $story_packages_image, $description_long, $display_order, $is_active, $sightseeing_json, $local_experiences_json, $terms_conditions, $id]'
new_update_params = '[$name, $slug, $tagline, $category, $altitude, $best_time, $duration, $cover_image, $hero_video_url, $story_narrative_image, $story_narrative_image_2, $story_narrative_image_3, $story_sightseeing_image, $story_packages_image, $description_long, $display_order, $is_active, $sightseeing_json, $local_experiences_json, $terms_conditions, $id]'
content = content.replace(old_update_params, new_update_params)

# If it is missing story_packages_image (because of the last change), let's handle both cases just in case
old_update2 = '"UPDATE destinations SET name=?, slug=?, tagline=?, category=?, altitude=?, best_time=?, duration=?, cover_image=?, hero_video_url=?, story_narrative_image=?, story_narrative_image_2=?, story_sightseeing_image=?, description_long=?, display_order=?, is_active=?, sightseeing_json=?, local_experiences_json=?, terms_conditions=? WHERE id=?"'
new_update2 = '"UPDATE destinations SET name=?, slug=?, tagline=?, category=?, altitude=?, best_time=?, duration=?, cover_image=?, hero_video_url=?, story_narrative_image=?, story_narrative_image_2=?, story_narrative_image_3=?, story_sightseeing_image=?, description_long=?, display_order=?, is_active=?, sightseeing_json=?, local_experiences_json=?, terms_conditions=? WHERE id=?"'
content = content.replace(old_update2, new_update2)

old_update_params2 = '[$name, $slug, $tagline, $category, $altitude, $best_time, $duration, $cover_image, $hero_video_url, $story_narrative_image, $story_narrative_image_2, $story_sightseeing_image, $description_long, $display_order, $is_active, $sightseeing_json, $local_experiences_json, $terms_conditions, $id]'
new_update_params2 = '[$name, $slug, $tagline, $category, $altitude, $best_time, $duration, $cover_image, $hero_video_url, $story_narrative_image, $story_narrative_image_2, $story_narrative_image_3, $story_sightseeing_image, $description_long, $display_order, $is_active, $sightseeing_json, $local_experiences_json, $terms_conditions, $id]'
content = content.replace(old_update_params2, new_update_params2)


# 4. Update the SQL INSERT queries
old_insert = '"INSERT INTO destinations (name, slug, tagline, category, altitude, best_time, duration, cover_image, hero_video_url, story_narrative_image, story_narrative_image_2, story_sightseeing_image, story_packages_image, description_long, display_order, is_active, sightseeing_json, parallax_layers_json, local_experiences_json, terms_conditions) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"'
new_insert = '"INSERT INTO destinations (name, slug, tagline, category, altitude, best_time, duration, cover_image, hero_video_url, story_narrative_image, story_narrative_image_2, story_narrative_image_3, story_sightseeing_image, story_packages_image, description_long, display_order, is_active, sightseeing_json, parallax_layers_json, local_experiences_json, terms_conditions) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"'
content = content.replace(old_insert, new_insert)

old_insert_params = '[$name, $slug, $tagline, $category, $altitude, $best_time, $duration, $cover_image, $hero_video_url, $story_narrative_image, $story_narrative_image_2, $story_sightseeing_image, $story_packages_image, $description_long, $display_order, $is_active, $sightseeing_json, "[]", $local_experiences_json, $terms_conditions]'
new_insert_params = '[$name, $slug, $tagline, $category, $altitude, $best_time, $duration, $cover_image, $hero_video_url, $story_narrative_image, $story_narrative_image_2, $story_narrative_image_3, $story_sightseeing_image, $story_packages_image, $description_long, $display_order, $is_active, $sightseeing_json, "[]", $local_experiences_json, $terms_conditions]'
content = content.replace(old_insert_params, new_insert_params)

# If missing story_packages_image in insert
old_insert2 = '"INSERT INTO destinations (name, slug, tagline, category, altitude, best_time, duration, cover_image, hero_video_url, story_narrative_image, story_narrative_image_2, story_sightseeing_image, description_long, display_order, is_active, sightseeing_json, parallax_layers_json, local_experiences_json, terms_conditions) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"'
new_insert2 = '"INSERT INTO destinations (name, slug, tagline, category, altitude, best_time, duration, cover_image, hero_video_url, story_narrative_image, story_narrative_image_2, story_narrative_image_3, story_sightseeing_image, description_long, display_order, is_active, sightseeing_json, parallax_layers_json, local_experiences_json, terms_conditions) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"'
content = content.replace(old_insert2, new_insert2)

old_insert_params2 = '[$name, $slug, $tagline, $category, $altitude, $best_time, $duration, $cover_image, $hero_video_url, $story_narrative_image, $story_narrative_image_2, $story_sightseeing_image, $description_long, $display_order, $is_active, $sightseeing_json, "[]", $local_experiences_json, $terms_conditions]'
new_insert_params2 = '[$name, $slug, $tagline, $category, $altitude, $best_time, $duration, $cover_image, $hero_video_url, $story_narrative_image, $story_narrative_image_2, $story_narrative_image_3, $story_sightseeing_image, $description_long, $display_order, $is_active, $sightseeing_json, "[]", $local_experiences_json, $terms_conditions]'
content = content.replace(old_insert_params2, new_insert_params2)


# 5. Add HTML for story_narrative_image_3
narrative_html = """                <div class="form-group">
                    <label>Narrative Section Image URL 2</label>
                    <input type="text" name="story_narrative_image_2" value="<?php echo htmlspecialchars($dest['story_narrative_image_2'] ?? ''); ?>">
                </div>"""
narrative_html_new = """                <div class="form-group">
                    <label>Narrative Section Image URL 2</label>
                    <input type="text" name="story_narrative_image_2" value="<?php echo htmlspecialchars($dest['story_narrative_image_2'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label>Narrative Section Image URL 3</label>
                    <input type="text" name="story_narrative_image_3" value="<?php echo htmlspecialchars($dest['story_narrative_image_3'] ?? ''); ?>">
                </div>"""
content = content.replace(narrative_html, narrative_html_new)


with open(filepath, 'w', encoding='utf-8') as f:
    f.write(content)
print("Updated destination-form.php")
