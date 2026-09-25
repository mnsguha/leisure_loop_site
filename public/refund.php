<?php 
$page_title = "Refund Policy | Leisure Loop Trip";
require_once '../includes/functions.php';
csrf_stamp_form();
include '../includes/header.php';
?>

    <section class="section" style="padding-top: 12rem;">
        <div class="container" style="max-width: 800px;">
            <span class="section-label">Legal</span>
            <h1 class="serif" style="font-size: 3rem; margin-bottom: 3rem;">Refund & Cancellation</h1>
            
            <div style="color: var(--text-muted); line-height: 1.8;">
                <p style="margin-bottom: 1.5rem;">We understand that plans can change. Our refund and cancellation policy is designed to be fair to both our travelers and our local partners.</p>

                <h3 style="color: #fff; margin: 2.5rem 0 1rem;">1. Cancellation by Traveler</h3>
                <ul style="margin-bottom: 1.5rem; padding-left: 1.5rem;">
                    <li style="margin-bottom: 0.5rem;">30+ days before departure: 90% refund of the total booking amount.</li>
                    <li style="margin-bottom: 0.5rem;">15-29 days before departure: 50% refund of the total booking amount.</li>
                    <li style="margin-bottom: 0.5rem;">Less than 15 days before departure: No refund.</li>
                </ul>

                <h3 style="color: #fff; margin: 2.5rem 0 1rem;">2. Cancellation by Leisure Loop Trip</h3>
                <p style="margin-bottom: 1.5rem;">In the rare event that we must cancel a journey due to unforeseen circumstances (e.g., natural disasters, government restrictions), travelers will receive a full refund or the option to reschedule.</p>

                <h3 style="color: #fff; margin: 2.5rem 0 1rem;">3. Process</h3>
                <p style="margin-bottom: 1.5rem;">Refund requests must be submitted in writing to curator@leisurelooptrip.in. Approved refunds will be processed within 7-10 business days.</p>

                <p style="margin-top: 4rem; font-size: 0.8rem;">Last updated: May 2026</p>
            </div>
        </div>
    </section>

<?php include '../includes/footer.php'; ?>
