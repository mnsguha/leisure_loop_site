<section id="splash-container" class="mobile-splash splash-background--1" aria-label="Welcome to Leisure Loop Trip">
    <input class="splash-step-toggle" id="splash-step-toggle" type="checkbox">
    <div class="mobile-splash__overlay" aria-hidden="true"></div>

    <!-- State 1: Intro Screen -->
    <div id="splash-state-1" class="mobile-splash__intro">
        <img class="mobile-splash__logo" src="assets/img/leisure.png" alt="Leisure Loop Trip">

        <h2 class="mobile-splash__eyebrow">Get ready for</h2>
        <h1 class="mobile-splash__title">New Adventures</h1>
        
        <p class="mobile-splash__description">
            If you like to travel, then this is for you! Here you can explore the beauty of the world.
        </p>
        
        <!-- Pagination Dots -->
        <div id="splash-dots" class="mobile-splash__dots" aria-label="Onboarding image selector">
            <button class="splash-dot is-active" data-splash-index="1" type="button" aria-label="Show image 1" aria-pressed="true"></button>
            <button class="splash-dot" data-splash-index="2" type="button" aria-label="Show image 2" aria-pressed="false"></button>
            <button class="splash-dot" data-splash-index="3" type="button" aria-label="Show image 3" aria-pressed="false"></button>
        </div>
        
        <label id="btn-lets-tour" class="mobile-splash__primary-button" for="splash-step-toggle">Let's Go</label>
    </div>

    <!-- State 2: Form Screen (Hidden initially) -->
    <div id="splash-state-2" class="mobile-splash__form-state" aria-hidden="true">
        <div class="mobile-splash__form-card">
            <h2 class="mobile-splash__form-title">Join the Journey</h2>
            <p class="mobile-splash__form-description">Enter your details to start exploring.</p>
            
            <form id="splash-signup-form">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                
<label for="splash_name" class="sr-only">Guest Name</label>
<input class="mobile-splash__input" type="text" id="splash_name" name="name" placeholder="Guest Name" autocomplete="name" required>
                
                
<label for="splash_phone" class="sr-only">Phone Number</label>
<input class="mobile-splash__input" type="tel" id="splash_phone" name="phone" placeholder="Phone Number" autocomplete="tel" required>
                
                
<label for="splash_email" class="sr-only">Email Address</label>
<input class="mobile-splash__input" type="email" id="splash_email" name="email" placeholder="Email Address" autocomplete="email" required>
                
                <button type="submit" id="splash_submit_btn" class="mobile-splash__submit-button">Start Exploring</button>
                
                <div class="mobile-splash__skip-wrap">
                    <a href="index.php?onboarding=skip" id="splash_skip" class="mobile-splash__skip-button">Skip for now</a>
                </div>
            </form>
        </div>
    </div>
</section>
