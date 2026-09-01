<?php
// includes/notice_popup.php
if (!isset($pdo)) {
    return; // Safety check
}

$popup_settings = null;
try {
    $stmt = $pdo->query("SELECT * FROM popup_settings WHERE id = 1 AND is_active = 1");
    $popup_settings = $stmt->fetch();
} catch (Exception $e) {
    // Ignore if table doesn't exist yet
}

if (!$popup_settings) {
    return;
}

// Fetch active destinations for the dropdown
$popup_destinations = [];
try {
    $popup_destinations = $pdo->query("SELECT name FROM destinations WHERE is_active = 1 ORDER BY display_order ASC")->fetchAll(PDO::FETCH_COLUMN);
} catch (Exception $e) {}

$img_url = htmlspecialchars(strpos($popup_settings['image_url'], 'http') === 0 ? $popup_settings['image_url'] : $popup_settings['image_url']);
if (empty($img_url)) {
    // Fallback image if none provided
    $img_url = 'https://images.unsplash.com/photo-1544735716-392fe2489ffa?q=80&w=600&auto=format&fit=crop';
}
?>

<!-- Notice Popup Styles -->
<style>
.notice-popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100vw;
    height: 100vh;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    opacity: 0;
    visibility: hidden;
    transition: opacity 0.4s ease, visibility 0.4s ease;
}
.notice-popup-overlay.active {
    opacity: 1;
    visibility: visible;
}
.notice-popup-container {
    background: #0f172a;
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    display: flex;
    max-width: 900px;
    width: 90%;
    max-height: 90vh;
    overflow: hidden;
    box-shadow: 0 20px 50px rgba(0,0,0,0.5);
    transform: translateY(20px) scale(0.95);
    transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
}
.notice-popup-overlay.active .notice-popup-container {
    transform: translateY(0) scale(1);
}
.notice-popup-close {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255,255,255,0.1);
    border: none;
    color: white;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    z-index: 10;
    transition: background 0.3s ease;
}
.notice-popup-close:hover {
    background: rgba(255,255,255,0.2);
}
.notice-popup-img {
    flex: 1;
    background-size: cover;
    background-position: center;
    min-height: 500px;
}
.notice-popup-content {
    flex: 1;
    padding: 3rem 2.5rem;
    display: flex;
    flex-direction: column;
    overflow-y: auto;
}
.notice-popup-content h2 {
    color: var(--gold);
    font-size: 1.8rem;
    margin-bottom: 1.5rem;
    font-weight: 500;
}
.notice-form .field-shell {
    margin-bottom: 1rem;
    position: relative;
}
.notice-form .field-shell input, 
.notice-form .field-shell select {
    width: 100%;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(255,255,255,0.1);
    color: white;
    padding: 0.8rem 1rem;
    border-radius: 8px;
    outline: none;
    transition: border-color 0.3s;
}
.notice-form .field-shell input:focus, 
.notice-form .field-shell select:focus {
    border-color: var(--gold);
}
.notice-form .form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}
.notice-form .btn-submit {
    width: 100%;
    background: var(--gold);
    color: black;
    border: none;
    padding: 1rem;
    border-radius: 8px;
    font-weight: 600;
    cursor: pointer;
    margin-top: 1rem;
    transition: opacity 0.3s;
}
.notice-form .btn-submit:hover {
    opacity: 0.9;
}

@media (max-width: 768px) {
    .notice-popup-container {
        flex-direction: column;
        max-height: 95vh;
    }
    .notice-popup-img {
        min-height: 200px;
        flex: none;
    }
    .notice-popup-content {
        padding: 2rem 1.5rem;
    }
}
</style>

<div class="notice-popup-overlay" id="noticePopup">
    <div class="notice-popup-container">
        <button class="notice-popup-close" id="noticePopupClose">&times;</button>
        <div class="notice-popup-img" style="background-image: url('<?php echo $img_url; ?>');"></div>
        <div class="notice-popup-content">
            <h2 class="serif-accent"><?php echo htmlspecialchars($popup_settings['title']); ?></h2>
            <form action="api/submit-lead.php" method="POST" class="notice-form js-lead-form">
                <input type="hidden" name="source" value="Notice Popup">
                <div class="field-shell">
                    <input type="text" name="name" placeholder="Your Full Name *" required>
                </div>
                <div class="field-shell">
                    <input type="tel" name="phone" placeholder="Phone Number *" required>
                </div>
                <div class="field-shell">
                    <select name="destination" required>
                        <option value="" disabled selected>-- Select Destination -- *</option>
                        <?php foreach($popup_destinations as $d): ?>
                            <option value="<?php echo htmlspecialchars($d); ?>"><?php echo htmlspecialchars($d); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="field-shell">
                    <input type="text" name="date" placeholder="Date of travelling *" onfocus="(this.type='date')" required>
                </div>
                <div class="form-row">
                    <div class="field-shell">
                        <select name="adults" required>
                            <option value="" disabled selected>Adults *</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5+">5+</option>
                        </select>
                    </div>
                    <div class="field-shell">
                        <select name="children">
                            <option value="" disabled selected>Children</option>
                            <option value="0">0</option>
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4+">4+</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn-submit">SUBMIT ENQUIRY</button>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check session storage to see if we've already shown it this session
    if (!sessionStorage.getItem('noticePopupShown')) {
        setTimeout(function() {
            document.getElementById('noticePopup').classList.add('active');
            sessionStorage.setItem('noticePopupShown', 'true');
        }, 2000); // 2 second delay
    }

    document.getElementById('noticePopupClose').addEventListener('click', function() {
        document.getElementById('noticePopup').classList.remove('active');
    });

    // Close on overlay click
    document.getElementById('noticePopup').addEventListener('click', function(e) {
        if (e.target === this) {
            this.classList.remove('active');
        }
    });
});
</script>
