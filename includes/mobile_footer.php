<?php
// includes/mobile_footer.php
$modal_prefix = 'mob-';
?>
    <!-- Universal Mobile Bottom Navigation & Drawer -->
    <?php if (empty($hide_bottom_nav)): ?>
    <?php include __DIR__ . '/mobile_bottom_nav.php'; ?>
    <?php endif; ?>

    <!-- Global App Modals & Notifications -->
    <?php include 'lead-modal.php'; ?>
    <?php include 'enquiry-modal.php'; ?>
    
    <div id="mob-toastContainer" class="toast-container"></div>

    <!-- Core App Scripts -->
    <script src="js/modules/mobile-views.js?v=<?= time() ?>" defer></script>
    <script src="js/modals.js?v=<?= time() ?>"></script>
    <?php if (isset($extra_scripts)) echo $extra_scripts; ?>
</body>
</html>
