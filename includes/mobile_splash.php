<div id="splash-container" class="mobile-splash-container">
    <!-- Dark Gradient Scrim -->
    <div class="splash-scrim"></div>

    <!-- State 1: Onboarding Intro -->
    <div id="splash-state-1" class="splash-state-content">
        <div class="splash-logo-wrap">
            <img src="assets/img/leisure.png" alt="Leisure Loop Trip" class="splash-brand-logo" onerror="this.onerror=null; this.src='assets/img/mobile_text.webp';">
        </div>

        <p class="splash-kicker">Get ready for</p>
        <h1 class="splash-title">
            New <span class="splash-title-accent">Adventures</span>
        </h1>
        <p class="splash-description">
            If you like to travel, then this is for you! Here you can explore the beauty of the world.
        </p>

        <!-- Horizontal Dot Indicators -->
        <div id="splash-dots" class="splash-dots">
            <button type="button" class="splash-dot is-active" data-splash-index="1" aria-label="Slide 1"></button>
            <button type="button" class="splash-dot" data-splash-index="2" aria-label="Slide 2"></button>
            <button type="button" class="splash-dot" data-splash-index="3" aria-label="Slide 3"></button>
        </div>

        <button type="button" id="btn-lets-tour" class="splash-btn-primary">
            Let's Go
        </button>
    </div>

    <!-- State 2: Guest Form (Initially Hidden via CSS class) -->
    <div id="splash-state-2" class="splash-state-content is-hidden">
        <div class="splash-form-card">
            <h2 class="splash-form-title">Join the Journey</h2>
            <p class="splash-form-subtitle">Enter your contact details to start exploring.</p>

            <form id="splash-signup-form" action="api-submit-lead.php" method="POST">
                <input type="text" name="fax_office" hidden tabindex="-1" autocomplete="off" aria-hidden="true">
                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
                <label for="splash-guest-name" class="sr-only">Guest Name</label>
                <input type="text" id="splash-guest-name" name="name" class="splash-input" placeholder="Guest Name" required>

                <label for="splash-guest-phone" class="sr-only">Phone Number</label>
                <input type="tel" id="splash-guest-phone" name="phone" class="splash-input" placeholder="Phone Number" required>

                <label for="splash-guest-email" class="sr-only">Email Address</label>
                <input type="email" id="splash-guest-email" name="email" class="splash-input" placeholder="Email Address">

                <button type="submit" class="splash-btn-submit">
                    Start Exploring
                </button>
                <div class="splash-skip-wrap">
                    <button type="button" id="splash_skip" class="splash-btn-skip">
                        Skip for now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
