<?php
$file = 'g:\Antigravity\leisure_loop_site\public\admin\hotels.php';
$content = file_get_contents($file);

// Replace Add New Hotel button with group
$old1 = <<<'EOD'
                <a href="hotel-form.php" class="btn-primary">+ Add New Hotel</a>
EOD;

$new1 = <<<'EOD'
                <div style="display: flex; flex-direction: column; gap: 10px; align-items: flex-end;">
                    <a href="hotel-form.php" class="btn-primary" style="width: 100%; text-align: center;">+ Add New Hotel</a>
                    <button onclick="openHotelSelectModal()" style="width: 100%; background: rgba(197,160,89,0.1); color: var(--gold); border: 1px solid var(--gold); padding: 12px 24px; border-radius: 6px; font-weight: 600; cursor: pointer; transition: 0.3s;"><i class="fas fa-calendar-alt"></i> Rates & Inventory</button>
                </div>
EOD;

$content = str_replace($old1, $new1, $content);

// Remove Inventory link
$old2 = <<<'EOD'
                            <a href="hotel-gallery.php?hotel_id=<?php echo $hotel['id']; ?>" class="action-link" style="color: #2ecc71;">Gallery</a> | 
                            <a href="hotel-inventory.php?hotel_id=<?php echo $hotel['id']; ?>" class="action-link" style="color: #f1c40f;">Inventory</a> | 
EOD;

$new2 = <<<'EOD'
                            <a href="hotel-gallery.php?hotel_id=<?php echo $hotel['id']; ?>" class="action-link" style="color: #2ecc71;">Gallery</a> | 
EOD;

$content = str_replace($old2, $new2, $content);

// Add Modal
$old3 = <<<'EOD'
    </div>
</body>
</html>
EOD;

$new3 = <<<'EOD'
    </div>

    <!-- Hotel Select Modal -->
    <div id="hotelSelectModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 1000; justify-content: center; align-items: center;">
        <div style="background: var(--secondary-dark); width: 500px; border-radius: 8px; box-shadow: 0 10px 30px rgba(0,0,0,0.5); padding: 30px; border: 1px solid rgba(255,255,255,0.1);">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h2 style="font-family: 'Playfair Display', serif; color: #fff; margin: 0; font-size: 1.8rem;">Select <span style="color: var(--gold);">Hotel</span></h2>
                <button type="button" onclick="closeHotelSelectModal()" style="background: transparent; border: none; color: #fff; font-size: 1.5rem; cursor: pointer;">&times;</button>
            </div>
            
            <div style="margin-bottom: 20px; position: relative;">
                <input type="text" id="hotelSearchInput" placeholder="Search hotel..." style="width: 100%; padding: 12px; border-radius: 6px; border: 1px solid rgba(255,255,255,0.2); background: rgba(0,0,0,0.2); color: #fff; font-size: 1rem; margin-bottom: 10px; outline: none;" onkeyup="filterHotels()">
                
                <div id="hotelList" style="max-height: 250px; overflow-y: auto; background: rgba(0,0,0,0.3); border-radius: 6px; border: 1px solid rgba(255,255,255,0.1);">
                    <?php foreach ($hotels as $h): ?>
                        <div class="hotel-option" onclick="selectHotel(<?php echo $h['id']; ?>, '<?php echo addslashes(htmlspecialchars($h['name'])); ?>', event)" style="padding: 12px 15px; cursor: pointer; border-bottom: 1px solid rgba(255,255,255,0.05); color: #fff; transition: background 0.2s;">
                            <?php echo htmlspecialchars($h['name']); ?>
                        </div>
                    <?php endforeach; ?>
                    <?php if(empty($hotels)): ?>
                        <div style="padding: 12px 15px; color: var(--text-muted);">No hotels found.</div>
                    <?php endif; ?>
                </div>
            </div>

            <form id="hotelSelectForm" action="hotel-inventory.php" method="GET">
                <input type="hidden" name="hotel_id" id="selectedHotelId" value="">
                <div style="text-align: right; margin-top: 30px;">
                    <button type="button" onclick="closeHotelSelectModal()" style="padding: 12px 24px; border-radius: 6px; cursor: pointer; margin-right: 10px; background: transparent; border: 1px solid var(--text-muted); color: var(--text-muted); font-weight: 600;">Cancel</button>
                    <button type="submit" id="goBtn" style="padding: 12px 24px; border-radius: 6px; cursor: pointer; background: var(--gold); color: #000; border: none; font-weight: 600; transition: 0.3s;" disabled>Open Inventory & Rates</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openHotelSelectModal() {
            document.getElementById('hotelSelectModal').style.display = 'flex';
            document.getElementById('hotelSearchInput').value = '';
            document.getElementById('hotelSearchInput').focus();
            filterHotels();
            document.getElementById('selectedHotelId').value = '';
            
            var goBtn = document.getElementById('goBtn');
            goBtn.disabled = true;
            goBtn.style.opacity = '0.5';
            goBtn.style.cursor = 'not-allowed';
            
            // Reset selection styles
            var options = document.getElementsByClassName('hotel-option');
            for (var i = 0; i < options.length; i++) {
                options[i].style.background = 'transparent';
                options[i].style.color = '#fff';
            }
        }

        function closeHotelSelectModal() {
            document.getElementById('hotelSelectModal').style.display = 'none';
        }

        function filterHotels() {
            var input, filter, div, options, i, txtValue;
            input = document.getElementById("hotelSearchInput");
            filter = input.value.toUpperCase();
            div = document.getElementById("hotelList");
            options = div.getElementsByClassName("hotel-option");
            for (i = 0; i < options.length; i++) {
                txtValue = options[i].textContent || options[i].innerText;
                if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    options[i].style.display = "";
                } else {
                    options[i].style.display = "none";
                }
            }
        }

        function selectHotel(id, name, event) {
            document.getElementById('selectedHotelId').value = id;
            var goBtn = document.getElementById('goBtn');
            goBtn.disabled = false;
            goBtn.style.opacity = '1';
            goBtn.style.cursor = 'pointer';
            
            var options = document.getElementsByClassName('hotel-option');
            for (var i = 0; i < options.length; i++) {
                options[i].style.background = 'transparent';
                options[i].style.color = '#fff';
            }
            event.currentTarget.style.background = 'rgba(197,160,89,0.3)';
            event.currentTarget.style.color = 'var(--gold)';
        }
    </script>
</body>
</html>
EOD;

$content = str_replace($old3, $new3, $content);

file_put_contents($file, $content);
echo "Patched hotels.php successfully.\n";
