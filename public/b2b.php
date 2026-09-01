<?php 
    require_once '../config/db.php';
    require_once '../config/recaptcha.php';
    
    // Detect Mobile
    $useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $is_mobile = preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) 
                 || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4));

    if ($is_mobile) {
        include '../includes/mobile_b2b.php';
        exit;
    }
    
    $page_title = "B2B Partner Program | Leisure Loop Trip";
    $use_recaptcha = recaptchaIsConfigured();
    $recaptcha_site_key = recaptchaSiteKey();
    include '../includes/header.php';
?>
<link rel="stylesheet" href="css/content-pages.css">


<div class="b2b-hero">
    <div class="b2b-hero-bg"></div>
    <div class="b2b-hero-overlay"></div>
    <div class="container" style="animation: fadeUp 1.5s ease forwards; opacity: 0; transform: translateY(30px); position: relative; z-index: 2;">
        <span class="section-label-gold" style="margin-bottom: 1.5rem; display: inline-block;">Premium DMC Partner</span>
        <h1 class="serif" style="font-size: clamp(3rem, 5vw, 5rem); line-height: 1.1; margin-bottom: 1.5rem; color: #fff;">Partner With Us. <br><span style="color: var(--gold); font-style: italic;">Grow Together.</span></h1>
    </div>
</div>

<section style="background-color: var(--obsidian, #050505); padding-bottom: 4rem;">
    <div class="container">
        
        <div class="b2b-intro">
            <h2 class="serif" style="font-size: 2.5rem; color: #fff; margin-bottom: 2rem;">Your Trusted DMC in the Himalayas</h2>
            <p>
                At Leisure Loop Trip, we empower travel agents and tour operators worldwide by serving as their flawless, on-ground extension in Sikkim, Darjeeling, and the Himalayas. 
                We understand that your clients' satisfaction reflects directly on your agency's reputation. That's why we offer strictly white-labeled services, highly competitive net B2B rates, and uncompromising luxury execution.
            </p>
        </div>

        <div class="benefits-grid">
            <div class="benefit-card">
                <div class="benefit-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path></svg>
                </div>
                <h3 class="benefit-card-title">Net B2B Rates</h3>
                <p class="benefit-card-desc">Maximize your agency's profit margins. We provide our registered B2B partners with highly competitive, direct DMC pricing with absolutely no hidden markups.</p>
            </div>
            
            <div class="benefit-card">
                <div class="benefit-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                </div>
                <h3 class="benefit-card-title">100% White-Label</h3>
                <p class="benefit-card-desc">Your brand, our execution. From airport pick-ups to personalized itineraries, our local staff represent your agency, ensuring your brand equity remains intact.</p>
            </div>
            
            <div class="benefit-card">
                <div class="benefit-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                </div>
                <h3 class="benefit-card-title">24/7 Ground Support</h3>
                <p class="benefit-card-desc">Rest easy knowing your clients are in safe hands. We provide dedicated, round-the-clock on-ground assistance to handle any requests or emergencies immediately.</p>
            </div>
            
            <div class="benefit-card">
                <div class="benefit-card-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </div>
                <h3 class="benefit-card-title">Local Expertise</h3>
                <p class="benefit-card-desc">Gain access to our deeply curated network. We secure the best rooms at luxury estates, private permits, and exclusive experiences that cannot be booked online.</p>
            </div>
        </div>

    </div>
</section>

<!-- B2B Registration Form Section -->
<section style="padding: 6rem 0; position: relative; overflow: hidden;">
    <div class="form-parallax-bg"></div>
    <div class="form-parallax-overlay"></div>
    <div class="container" style="position: relative; z-index: 2;">
        <div style="text-align: center; margin-bottom: 4rem;">
            <span class="section-label-gold">Register Agency</span>
            <h2 class="serif" style="font-size: 3rem; color: #fff;">Become A Partner</h2>
        </div>

        <div class="b2b-form-wrapper">
            <form id="b2bForm" action="../api/submit-lead.php" method="POST" class="js-lead-form">
<input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

                
                <div class="grid-2-cols">
                    <div class="form-group-float">
                        <input type="text" id="b2b_agency_name" class="form-control-float" placeholder=" " required>
                        <label for="b2b_agency_name" class="form-label-float">Agency / Company Name</label>
                    </div>
                    <div class="form-group-float">
                        <input type="text" name="name" id="b2b_contact_person" class="form-control-float" placeholder=" " required>
                        <label for="b2b_contact_person" class="form-label-float">Contact Person Name</label>
                    </div>
                </div>

                <div class="grid-2-cols">
                    <div class="form-group-float">
                        <input type="email" name="email" id="b2b_email" class="form-control-float" placeholder=" " required>
                        <label for="b2b_email" class="form-label-float">Official Email Address</label>
                    </div>
                    <div class="form-group-float">
                        <input type="tel" name="phone" id="b2b_phone" class="form-control-float" placeholder=" " required>
                        <label for="b2b_phone" class="form-label-float">Phone / WhatsApp Number</label>
                    </div>
                </div>
                
                <div class="grid-2-cols">
                    <div class="form-group-float">
                        <input type="url" id="b2b_website" class="form-control-float" placeholder=" ">
                        <label for="b2b_website" class="form-label-float">Website URL (Optional)</label>
                    </div>
                    <div class="form-group-float">
                        <select id="b2b_volume" class="form-control-float" required style="color: #fff; background-color: var(--obsidian, #050505);">
                            <option value="" disabled selected hidden>Estimated Monthly Queries</option>
                            <option value="1-5">1 - 5 Queries</option>
                            <option value="6-15">6 - 15 Queries</option>
                            <option value="16-30">16 - 30 Queries</option>
                            <option value="30+">30+ Queries</option>
                        </select>
                        <label for="b2b_volume" class="form-label-float" style="top: -15px; font-size: 0.8rem; color: var(--gold);">Estimated Monthly Queries</label>
                    </div>
                </div>

                <div class="form-group-float" style="margin-top: 1rem;">
                    <textarea id="b2b_message" class="form-control-float" rows="4" placeholder=" " required></textarea>
                    <label for="b2b_message" class="form-label-float">How can we help you? (Specific requirements)</label>
                </div>

                <!-- Hidden field to hold the combined message -->
                <input type="hidden" name="message" id="b2b_combined_message" value="">
                <!-- Hidden fields for tracking source -->
                <input type="hidden" name="enforce_recaptcha" value="1">
                <?php if (!empty($use_recaptcha) && !empty($recaptcha_site_key)): ?>
                <div class="form-group-float recaptcha-shell" style="margin-bottom: 1.5rem;">
                    <div class="g-recaptcha" data-sitekey="<?php echo htmlspecialchars($recaptcha_site_key); ?>" data-theme="dark"></div>
                </div>
                <?php endif; ?>

                <button type="submit" class="btn-gold" style="width: 100%; border: none; font-size: 1.1rem; letter-spacing: 0.1em; margin-top: 1rem;">
                    SUBMIT REGISTRATION
                </button>
            </form>
        </div>
    </div>
</section>

<script src="js/modules/content-pages.js" defer></script>

<?php include '../includes/footer.php'; ?>
