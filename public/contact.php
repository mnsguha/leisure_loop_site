<?php 
    require_once '../includes/functions.php';
    csrf_stamp_form();

    require_once '../config/db.php';
    $page_title = "Contact Our Curators | Leisure Loop Trip";

    // Detect Mobile
    $useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $is_mobile = preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) 
                || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4));

    if ($is_mobile) {
        include '../includes/mobile_contact.php';
        exit;
    }

    include '../includes/header.php';
?>
<link rel="stylesheet" href="css/contact.css">


<div class="contact-hero-wrapper">
    <div class="contact-bg-image"></div>
    <div class="contact-bg-overlay"></div>
    
    <div class="container contact-container">
        <div class="contact-grid">
            
            <div class="contact-info-block">
                <div class="contact-info-bg"></div>
                <div class="contact-info-overlay"></div>
                <div class="contact-info-content">
                    <span class="section-label-gold contact-assistance-label">Curated Assistance</span>
                    <h1 class="contact-title">Design Your <br><span class="serif">Next Escape.</span></h1>
                    <p class="contact-subtitle">Ready to embark on a journey like no other? Our expert curators are standing by to craft your bespoke, once-in-a-lifetime itinerary.</p>
                    
                    <div class="contact-sla-banner">
                        <svg viewBox="0 0 24 24" width="16" height="16" stroke="var(--gold)" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                        <span class="contact-sla-text">Expect a personalized consultation within 2-4 hours.</span>
                    </div>
                    
                    <div class="contact-details-grid">
                        <div class="contact-detail-item">
                            <span class="contact-detail-label">Direct Lines</span>
                            <div class="contact-detail-text">+91 89189 21629</div>
                            <div class="contact-detail-subtext">curator@leisurelooptrip.in</div>
                        </div>
                        
                        <div class="contact-detail-item">
                            <span class="contact-detail-label">Headquarters</span>
                            <div class="contact-detail-text">Siliguri, West Bengal</div>
                            <div class="contact-detail-subtext">Gateways to the Eastern Himalayas</div>
                        </div>

                        <div class="contact-detail-item contact-office-hours">
                            <span class="contact-detail-label">Office Hours</span>
                            <div class="contact-detail-text">Monday - Saturday</div>
                            <div class="contact-detail-subtext">10:00 AM - 7:00 PM (IST)</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="premium-contact-form">
                <form id="contactForm" action="api-submit-lead.php" method="POST" class="js-lead-form">
                    <input type="text" name="fax_office" hidden tabindex="-1" autocomplete="off" aria-hidden="true">
                    <div class="form-group-float">
                        <input type="text" name="name" id="name" class="form-control-float" placeholder=" " required aria-required="true">
                        <label for="name" class="form-label-float">Your Name</label>
                    </div>
                    
                    <div class="form-group-float">
                        <input type="email" name="email" id="email" class="form-control-float" placeholder=" " required aria-required="true">
                        <label for="email" class="form-label-float">Email Address</label>
                    </div>
                    
                    <div class="form-group-float">
                        <input type="tel" name="phone" id="phone" class="form-control-float" placeholder=" " required aria-required="true">
                        <label for="phone" class="form-label-float">Phone Number</label>
                    </div>
                    
                    <div class="form-group-float">
                        <textarea name="message" id="message" class="form-control-float" placeholder=" " required aria-required="true"></textarea>
                        <label for="message" class="form-label-float">Your Requirements / Destinations</label>
                    </div>
                    
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'); ?>">
                    
                    <button type="submit" class="btn-submit-premium">Submit Inquiry</button>
                </form>
            </div>

        </div>
    </div>
</div>

<!-- Trust Signals Section -->
<section class="trust-indicators-wrapper">
    <div class="container">
        <div class="trust-grid">
            <div class="trust-item">
                <div class="trust-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path><polyline points="9 12 11 14 15 10"></polyline></svg>
                </div>
                <h4 class="trust-title">Recognized Partner</h4>
                <p class="trust-desc">Officially recognized travel partner with top industry accreditations.</p>
            </div>
            <div class="trust-item">
                <div class="trust-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                </div>
                <h4 class="trust-title">5-Star Excellence</h4>
                <p class="trust-desc">Rated 4.9/5 by over 500+ satisfied elite travelers globally.</p>
            </div>
            <div class="trust-item">
                <div class="trust-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path></svg>
                </div>
                <h4 class="trust-title">Secure & Private</h4>
                <p class="trust-desc">Your personal data and travel plans are held with the strictest confidentiality.</p>
            </div>
            <div class="trust-item">
                <div class="trust-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                </div>
                <h4 class="trust-title">24/7 Concierge</h4>
                <p class="trust-desc">A dedicated team available around the clock throughout your journey.</p>
            </div>
        </div>
    </div>
</section>

<!-- Location Map Section -->
<section class="contact-map-section">
    <div class="container">
        <div class="contact-map-wrapper">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d114065.17696236941!2d88.351717!3d26.727101!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x39e44114f5441d8f%3A0x7d6f5f9ebdc932e!2sSiliguri%2C%20West%20Bengal!5e0!3m2!1sen!2sin!4v1717650000000!5m2!1sen!2sin" width="100%" height="100%" class="contact-map-iframe" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            <div class="contact-map-overlay"></div>
        </div>
    </div>
</section>

<?php include '../includes/footer.php'; ?>
