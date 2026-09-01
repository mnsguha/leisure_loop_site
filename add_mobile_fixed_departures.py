import re

file_path = 'includes/mobile_home.php'
with open(file_path, 'r', encoding='utf-8') as f:
    content = f.read()

target_marker = "<!-- 7. Hotel Partners -->"
target_idx = content.find(target_marker)

if target_idx != -1:
    new_section = """
<!-- ✦ Fixed Departures Section (Mobile) ✦ -->
<?php if (!empty($fixed_departures)): ?>
<section id="mobile-fixed-departures" style="padding: 24px 16px; background-color: #050a14; position: relative; overflow: hidden; border-top: 1px solid rgba(197, 160, 89, 0.08);">
    
    <!-- Header -->
    <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-bottom: 20px;">
        <div>
            <span style="font-size: 0.65rem; color: var(--gold); text-transform: uppercase; letter-spacing: 0.1em; font-weight: 600; display: block; margin-bottom: 4px;">LIMITED AVAILABILITY</span>
            <h2 style="font-size: 1.4rem; color: #fff; margin-bottom: 0; font-family: var(--font-serif); line-height: 1.15;">Upcoming Fixed <span style="color: var(--gold); font-style: italic;">Departures</span></h2>
        </div>
    </div>

    <!-- Fixed Departures Carousel -->
    <div style="display: flex; gap: 12px; overflow-x: auto; scrollbar-width: none; -ms-overflow-style: none; padding-bottom: 12px; scroll-snap-type: x mandatory; padding-right: 24px;">
        <?php foreach ($fixed_departures as $fd): 
            $img = preg_match('/^https?:\\/\\//i', $fd['image_url']) ? $fd['image_url'] : ltrim($fd['image_url'], '/');
            
            $seats_left = $fd['available_seats'];
            $status = $fd['status'];
            $status_color = 'var(--gold)';
            $status_bg = 'rgba(197, 160, 89, 0.8)';
            if ($status == 'Sold Out') {
                $status_color = '#fff';
                $status_bg = 'rgba(239, 68, 68, 0.8)';
            } elseif ($status == 'Filling Fast' || $seats_left <= 5) {
                $status_color = '#fff';
                $status_bg = 'rgba(245, 158, 11, 0.8)';
            }
            
            // Format dates
            $date_str = date('M d', strtotime($fd['start_date'])) . ' - ' . date('M d, Y', strtotime($fd['end_date']));
        ?>
        <div onclick="window.location.href='contact.php'" style="scroll-snap-align: center; flex: 0 0 85%; position: relative; height: 340px; border-radius: 16px; overflow: hidden; border: 1px solid rgba(197, 160, 89, 0.15); box-shadow: 0 8px 20px rgba(0,0,0,0.4); cursor: pointer;">
            <!-- Backdrop -->
            <div style="position: absolute; inset: 0; background: url('<?php echo htmlspecialchars($img); ?>') no-repeat center center; background-size: cover;"></div>
            <!-- Overlay -->
            <div style="position: absolute; inset: 0; background: linear-gradient(180deg, rgba(5,10,20,0) 0%, rgba(5,10,20,0.7) 50%, rgba(5,10,20,0.95) 100%); z-index: 1;"></div>
            
            <!-- Badge -->
            <div style="position: absolute; top: 12px; right: 12px; background: <?php echo $status_bg; ?>; padding: 4px 10px; border-radius: 50px; color: <?php echo $status_color; ?>; font-size: 0.6rem; font-weight: 700; letter-spacing: 0.05em; text-transform: uppercase; backdrop-filter: blur(8px); z-index: 2; box-shadow: 0 4px 10px rgba(0,0,0,0.2);">
                <?php echo htmlspecialchars($status); ?> <?php if ($status != 'Sold Out') echo " &bull; " . $seats_left . " LEFT"; ?>
            </div>

            <!-- Content -->
            <div style="position: absolute; bottom: 0; left: 0; width: 100%; padding: 16px; z-index: 2; box-sizing: border-box; display: flex; flex-direction: column; gap: 6px;">
                <div style="display: flex; align-items: center; gap: 6px; color: var(--gold); font-size: 0.75rem; font-weight: 600;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                    <?php echo $date_str; ?>
                </div>
                <h3 style="font-family: var(--font-serif); font-size: 1.3rem; font-weight: 500; color: #ffffff; margin: 0; line-height: 1.2; text-shadow: 0 2px 4px rgba(0,0,0,0.8);"><?php echo htmlspecialchars($fd['title']); ?></h3>
                
                <div style="display: flex; justify-content: space-between; align-items: flex-end; margin-top: 4px;">
                    <div style="display: flex; align-items: center; gap: 4px; color: #94a3b8; font-size: 0.75rem;">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <?php echo htmlspecialchars($fd['duration']); ?>
                    </div>
                    <div style="font-size: 1rem; font-weight: 700; color: #fff;">
                        <span style="font-size: 0.65rem; color: #94a3b8; font-weight: 400; text-transform: uppercase;">From</span> 
                        &#8377;<?php echo number_format($fd['price']); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</section>
<?php endif; ?>

"""
    new_content = content[:target_idx] + new_section + content[target_idx:]
    with open(file_path, 'w', encoding='utf-8') as f:
        f.write(new_content)
    print("Added fixed departures correctly")
else:
    print("Could not find marker")
