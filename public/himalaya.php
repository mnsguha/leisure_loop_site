<?php
    require_once '../config/db.php';
    $page_title = "Sikkim Circuit | Elevation Scroll | Leisure Loop Trip";
    $meta_desc  = "An immersive 3D parallax journey from Gangtok's subtropical valleys to the sacred shores of Gurudongmar Lake. Sikkim Circuit by Leisure Loop Trip.";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <meta name="description" content="<?php echo $meta_desc; ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&family=Playfair+Display:ital,wght@0,400;0,700;0,900;1,400&display=swap" rel="stylesheet">

</head>
<body>

<?php include '../includes/header.php';
?>
<link rel="stylesheet" href="css/style.css?v=<?php echo time(); ?>">
<link rel="stylesheet" href="css/destinations.css">



<!-- ══════════════════════════════════════════════════════════
     ELEVATION SCROLL HERO
     Implementation per production spec:
     • journey-viewport → GSAP pins this element
     • scroll-wrapper   → transform-style: preserve-3d
     • 3 layers         → fly-through on scroll
     • journey-title    → fades + scales down first
     ══════════════════════════════════════════════════════════ -->
<div class="journey-viewport" id="journeyViewport">

    <div class="scroll-wrapper" id="scrollWrapper">

        <div class="layer layer-bg" id="layer1" data-depth="0.05"></div>
        <div class="layer layer-valley" id="layer2" data-depth="0.1"></div>
        <div class="layer layer-monastery" id="layer3" data-depth="0.4"></div>
        <div class="layer layer-pine" id="layer4" data-depth="0.8"></div>
        <div class="layer layer-fg" id="layer5" data-depth="1.2"></div>

        <!-- ── Overlay: vignette + text ── -->
        <div class="journey-overlay">
            <div class="alt-pill" id="altPill">
                <span>Summit</span>
                <strong>5,430 m</strong>
            </div>
            <span class="journey-eyebrow" id="journeyEye">
                The Sikkim Circuit · Leisure Loop Trip
            </span>
            <!-- Per specification: this is the .journey-title element -->
            <h1 class="journey-title" id="journeyTitle">
                Ascend to<br><em>the Peaks</em>
            </h1>
            <p class="journey-subtitle" id="journeySub">
                The snow-capped summit of Gurudongmar at 5,430 metres.
                Scroll to fly through the layers — valleys, passes,
                and forests — down to Gangtok's subtropical gateway.
            </p>
            <div class="journey-btns" id="journeyBtns">
                <a href="packages.php?destination=Sikkim" class="btn-p">Book the Sikkim Circuit</a>
                <a href="#journey" class="btn-g">
                    Explore the Circuit
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
            </div>
        </div>

        <!-- Scroll hint -->
        <div class="scroll-hint" id="scrollHint">
            <div class="sh-line"></div>
            <span class="sh-label">Scroll to ascend</span>
        </div>

    </div><!-- /scroll-wrapper -->

</div><!-- /journey-viewport -->

<!-- Fixed elevation sidebar -->
<div class="elev-bar" id="elevBar">
    <div class="elbl elbl-top">5,430m<br>Gurudongmar</div>
    <div class="e-track">
        <div class="e-prog" id="eProg"></div>
        <div class="e-current" id="eCurrent">280m</div>
    </div>
    <div class="elbl elbl-bot">280m<br>Siliguri</div>
</div>


<!-- ══════════════════════════════════════
     SECTION 2: JOURNEY STAGE CARDS
     ══════════════════════════════════════ -->
<section class="journey-section" id="journey">
    <div class="container">
        <span class="sh-eye">The Elevation Circuit</span>
        <h2 class="sh-hed">From <em>Summit to Valley</em></h2>
        <p class="sh-sub">Three distinct worlds across 3,780 metres of elevation change.</p>

        <div class="grid3">

            <div class="card rv">
                <img src="images/destinations/sikkim/scene1.jpg" alt="Gurudongmar" loading="lazy">
                <div class="cbody">
                    <div class="ctag">Stage 01 — The Summit</div>
                    <h3 class="ctitle">Gurudongmar <em>Lake</em></h3>
                    <p class="cdesc">One of the world's highest lakes. Sacred. Silent.
                        The waters reflect the peaks in absolute stillness.</p>
                    <div class="cfoot">
                        <div><div class="calbl">Altitude</div><div class="calt">5,430 m</div></div>
                        <a href="packages.php?destination=Sikkim" class="clink">Explore →</a>
                    </div>
                </div>
            </div>

            <div class="card rv">
                <img src="images/destinations/sikkim/scene3.jpg" alt="Yumthang Valley" loading="lazy">
                <div class="cbody">
                    <div class="ctag">Stage 02 — The Valley</div>
                    <h3 class="ctitle">Yumthang <em>Valley</em></h3>
                    <p class="cdesc">Breaking through the cloud layer. Alpine meadows
                        and the famous Valley of Flowers bloom in colour.</p>
                    <div class="cfoot">
                        <div><div class="calbl">Altitude</div><div class="calt">3,564 m</div></div>
                        <a href="packages.php?destination=Sikkim" class="clink">Explore →</a>
                    </div>
                </div>
            </div>

            <div class="card rv">
                <img src="images/destinations/sikkim/scene2.jpg" alt="Gangtok" loading="lazy">
                <div class="cbody">
                    <div class="ctag">Stage 03 — The Gateway</div>
                    <h3 class="ctitle">Gangtok <em>Gateway</em></h3>
                    <p class="cdesc">Colonial-era architecture, vibrant markets,
                        and the first dramatic Kanchenjunga sunrise views.</p>
                    <div class="cfoot">
                        <div><div class="calbl">Altitude</div><div class="calt">1,650 m</div></div>
                        <a href="packages.php?destination=Sikkim" class="clink">Explore →</a>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>


<!-- ══════════════════════════════════════
     SECTION 3: LAKE REVEAL
     ══════════════════════════════════════ -->
<section class="lake-section" id="lakeReveal">
    <img id="lakeBg" src="images/destinations/sikkim/scene1.jpg" alt="Gurudongmar Lake">
    <div class="lk-ov"></div>
    <div class="lk-ct">
        <span class="eye">The Sacred Summit — 5,430 Metres</span>
        <h2>Gurudongmar <em>Lake</em></h2>
        <p style="color:rgba(255,255,255,0.55);max-width:420px;margin:0.8rem auto 0;font-size:0.95rem;line-height:1.75;">
            Sacred. Silent. One of the highest lakes on Earth.
        </p>
        <div class="lk-stats">
            <div class="lk-stat"><span>Altitude</span><strong>5,430m</strong></div>
            <div class="lk-stat"><span>District</span><strong>North Sikkim</strong></div>
            <div class="lk-stat"><span>Best Season</span><strong>Apr – Jun</strong></div>
        </div>
    </div>
</section>


<!-- ══════════════════════════════════════
     SECTION 4: CTA
     ══════════════════════════════════════ -->
<section class="cta-section" id="book">
    <div class="container">
        <div class="cta-box rv">
            <h2>Ready for the <em>Elevation Journey?</em></h2>
            <p>Our Sikkim circuit specialists handle permits,
                altitude-acclimatised itineraries, and premium
                stays at every stage of the ascent.</p>
            <div class="cta-actions">
                <a href="packages.php?destination=Sikkim" class="btn-cta">View Sikkim Packages</a>
                <a href="https://wa.me/918918921629?text=Hi%20Leisure%20Loop%2C%20interested%20in%20Sikkim%20Circuit"
                   class="btn-wa" target="_blank">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    Chat on WhatsApp
                </a>
            </div>
        </div>
    </div>
</section>


<?php include '../includes/footer.php'; ?>


<!-- ══════════════════════════════════════════════════════
     PRODUCTION-READY GSAP TIMELINE
     Directly from specification with exact values.
     ══════════════════════════════════════════════════════ -->
<script src="js/modules/destinations.js" defer></script>
</body>
</html>
