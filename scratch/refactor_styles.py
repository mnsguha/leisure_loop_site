import sys
import re

def process_file(filepath, replacements):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = f.read()
    
    for old, new in replacements:
        content = content.replace(old, new)
        
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)
        
def main():
    # 1. package-form.php
    form_replacements = [
        ('<div class="header" style="margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid rgba(255,255,255,0.1);">', '<div class="header admin-page-header">'),
        ('<div style="display: flex; align-items: center; gap: 15px;">', '<div class="header-title-wrapper">'),
        ('<a href="packages.php" style="color:var(--gold); font-size: 1.8rem; text-decoration: none; line-height: 1;" title="Back to Packages">←</a>', '<a href="packages.php" class="back-arrow" title="Back to Packages">←</a>'),
        ('<h1 style="margin: 0;">', '<h1 class="header-title">'),
        ('<form method="POST" enctype="multipart/form-data" style="display: flex; flex-direction: column; gap: 2rem;">', '<form method="POST" enctype="multipart/form-data" class="admin-form-layout">'),
        ('<h3 style="margin-bottom: 1rem; padding-bottom: 0.5rem; border-bottom: 1px solid rgba(255,255,255,0.1);">', '<h3 class="form-panel-title">'),
        ('<div class="form-row" style="grid-template-columns: 1fr 1fr;">', '<div class="form-row form-row-2col">'),
        ('<div style="display: flex; gap: 1rem; align-items: center; margin-top: 0.5rem;">', '<div class="radio-group">'),
        ('<label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">', '<label class="radio-label">'),
        ('style="width: auto; appearance: auto; margin: 0;"', 'class="input-radio"'),
        ('<span style="color:#64748b;font-weight:400;font-size:0.8rem;">', '<span class="label-hint">'),
        ('<div id="fixed-departure-fields" style="display: none; padding-top: 15px; border-top: 1px dashed rgba(255,255,255,0.1);">', '<div id="fixed-departure-fields" class="conditional-fields">'),
        ('<h4 style="margin-bottom: 15px; color: #4ade80;">', '<h4 class="conditional-fields-title">'),
        ('<span style="font-size:0.8rem; font-weight:normal; color:var(--text-muted);">', '<span class="label-hint">'),
        ('<div class="form-group" style="display: flex; flex-direction: column; gap: 1rem;">', '<div class="form-group checkbox-group-col">'),
        ('style="width: auto;"', 'class="input-checkbox"'),
        ('<label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer; color: #fb923c; font-weight: 600;">', '<label class="checkbox-label-highlight">'),
        ('<div id="itinerary-container" style="margin-top: 1.5rem;">', '<div id="itinerary-container" class="mt-4">'),
        ('<button type="button" id="add-day" class="btn-outline" style="width: 100%;">', '<button type="button" id="add-day" class="btn-outline w-full">'),
        ('<div class="form-card" style="background: transparent; border: none; padding: 0;">', '<div class="form-card form-card-transparent">'),
        ('<button type="submit" class="btn-primary" style="width: 100%; padding: 1.25rem;">', '<button type="submit" class="btn-primary btn-block-large">'),
    ]
    process_file('g:/Antigravity/leisure_loop_site/public/admin/package-form.php', form_replacements)

    # 2. package-gallery.php
    gallery_replacements = [
        ('<div class="header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem; padding-bottom: 2rem; border-bottom: 1px solid rgba(255,255,255,0.1);">', '<div class="header admin-page-header-split">'),
        ('<div style="display: flex; align-items: center; gap: 15px;">', '<div class="header-title-wrapper">'),
        ('<a href="packages.php" style="color:var(--gold); font-size: 1.8rem; text-decoration: none; line-height: 1;" title="Back to Packages">←</a>', '<a href="packages.php" class="back-arrow" title="Back to Packages">←</a>'),
        ('<h1 style="margin: 0;">', '<h1 class="header-title">'),
        ('<p class="muted" style="margin: 5px 0 0 0;">', '<p class="muted header-subtitle">'),
        ('<div style="background: var(--dark-surface); padding: 30px; border-radius: 12px; margin-bottom: 30px; border: 1px solid rgba(255,255,255,0.05); display: flex; flex-direction: column; gap: 20px;">', '<div class="upload-panel">'),
        ('<form action="" method="POST" enctype="multipart/form-data" style="display: flex; align-items: center; gap: 20px;">', '<form action="" method="POST" enctype="multipart/form-data" class="upload-form-row">'),
        ('<div style="flex: 1;">', '<div class="flex-1">'),
        ('<label style="display: block; font-size: 0.85rem; color: rgba(255,255,255,0.7); margin-bottom: 8px;">', '<label class="upload-label">'),
        ('style="width: 100%; padding: 12px; background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; color: #fff;"', 'class="upload-input"'),
        ('<button type="submit" class="btn-primary" style="white-space: nowrap; margin-top: 25px;">', '<button type="submit" class="btn-primary btn-upload">'),
        ('<div style="text-align: center; color: rgba(255,255,255,0.5); font-size: 0.9rem;">', '<div class="upload-divider">'),
        ('<form action="" method="POST" style="display: flex; align-items: center; gap: 20px;">', '<form action="" method="POST" class="upload-form-row">'),
        ('<h2 style="font-family: \'Playfair Display\', serif; font-size: 2rem; color: #fff; margin-bottom: 20px;">', '<h2 class="gallery-title">'),
        ('<div style="padding: 20px; background: rgba(255,255,255,0.05); border-radius: 8px; color: rgba(255,255,255,0.7);">', '<div class="empty-state">')
    ]
    process_file('g:/Antigravity/leisure_loop_site/public/admin/package-gallery.php', gallery_replacements)

    # 3. packages.php
    packages_replacements = [
        ('<div class="header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 3rem;">', '<div class="header admin-page-header-flex">'),
        ('<td style="width: 80px;">', '<td class="col-thumb">'),
        ('<div style="width: 60px; height: 60px; border-radius: 12px; background: url(\'', '<div class="table-thumb" style="background-image: url(\''),
        ('\') center/cover;"></div>', '\');"></div>'),
        ('<span style="font-size: 0.8rem; color: var(--text-muted);">', '<span class="text-muted-sm">'),
        ('<span style="font-size: 0.75rem; color: var(--accent);">', '<span class="text-accent-xs">'),
        ('<span style="text-decoration: line-through; color: var(--text-muted); font-size: 0.8rem;">', '<span class="price-crossed">'),
        ('style="background: rgba(197, 160, 89, 0.2); color: var(--gold); border: 1px solid rgba(197, 160, 89, 0.3); margin-top: 5px; display: inline-block;"', 'class="badge-block status-international"'),
        ('style="background: rgba(249, 115, 22, 0.25); color: #fb923c; border: 1px solid rgba(249, 115, 22, 0.45); margin-top: 5px; display: inline-block;"', 'class="badge-block badge-trending"'),
        ('style="background: rgba(11, 124, 74, 0.2); color: #4ade80; border: 1px solid rgba(11, 124, 74, 0.4); margin-top: 5px; display: inline-block;"', 'class="badge-block badge-fixed"'),
        ('<div style="display: flex; flex-direction: column; gap: 8px; align-items: stretch; max-width: 170px; text-align: center;">', '<div class="action-buttons-col">'),
        ('style="background: rgba(14, 165, 233, 0.2); color: #38bdf8; border: 1px solid rgba(14, 165, 233, 0.4);"', 'class="btn-gallery"'),
        ('style="background: rgba(249, 115, 22, 0.2); color: #fb923c; border: 1px solid rgba(249, 115, 22, 0.4);"', 'class="btn-trending-on"'),
        ('style="background: rgba(255, 255, 255, 0.06); color: var(--text-muted); border: 1px solid rgba(255, 255, 255, 0.15);"', 'class="btn-trending-off"'),
        ('<td colspan="5" style="text-align: center; padding: 4rem; color: var(--text-muted);">', '<td colspan="5" class="table-empty">'),
    ]
    process_file('g:/Antigravity/leisure_loop_site/public/admin/packages.php', packages_replacements)

    print("Success")

if __name__ == '__main__':
    main()
