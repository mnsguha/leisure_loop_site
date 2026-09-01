<?php 
    require_once '../config/db.php';
    require_once '../includes/data_faqs.php';
    
    $page_title = "Knowledge Base | Leisure Loop Trip";
    
    // Detect Mobile
    $useragent = $_SERVER['HTTP_USER_AGENT'] ?? '';
    $is_mobile = preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $useragent) 
                || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i', substr($useragent, 0, 4));

    if ($is_mobile) {
        include '../includes/mobile_faq.php';
        exit;
    }

    include '../includes/header.php';
?>
<link rel="stylesheet" href="css/content-pages.css">


<div class="faq-hero">
    <div class="faq-hero-bg"></div>
    <div class="faq-hero-overlay"></div>
    <div class="container" style="animation: fadeUp 1.5s ease forwards; opacity: 0; transform: translateY(30px); position: relative; z-index: 2;">
        <span class="section-label-gold" style="margin-bottom: 1.5rem; display: inline-block;">Knowledge Base</span>
        <h1 class="serif" style="font-size: clamp(3rem, 5vw, 5rem); line-height: 1.1; margin-bottom: 1.5rem; color: #fff;">Common <br><span style="color: var(--gold); font-style: italic;">Curiosities.</span></h1>
        <p style="color: rgba(255,255,255,0.7); max-width: 600px; margin: 0 auto; font-size: 1.1rem; line-height: 1.6;">Everything you need to know about crafting your bespoke journey with Leisure Loop Trip.</p>
    </div>
</div>

<div class="faq-page-content" style="background-color: var(--obsidian, #050505);">
    <!-- FAQ Section identical to index.php -->
    <section class="faq-section" style="position: relative; overflow: hidden;">
        <div class="faq-watermark" id="faqWatermark" style="top: -50px;">?</div>
        <div class="container" style="position: relative; z-index: 1;">
            <div class="faq-grid">
                <?php
                    $half = ceil(count($faqs) / 2);
                    $left_col = array_slice($faqs, 0, $half);
                    $right_col = array_slice($faqs, $half);
                ?>
                <div class="faq-col">
                    <?php foreach ($left_col as $faq): ?>
                    <div class="faq-item">
                        <div class="faq-question"><h4><?php echo htmlspecialchars($faq['question']); ?></h4><div class="faq-icon">&#10010;</div></div>
                        <div class="faq-answer"><p><?php echo htmlspecialchars($faq['answer']); ?></p></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="faq-col">
                    <?php foreach ($right_col as $faq): ?>
                    <div class="faq-item">
                        <div class="faq-question"><h4><?php echo htmlspecialchars($faq['question']); ?></h4><div class="faq-icon">&#10010;</div></div>
                        <div class="faq-answer"><p><?php echo htmlspecialchars($faq['answer']); ?></p></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </section>
</div>

<!-- Scripts for FAQ accordion functionality (copied from main layout if necessary, though it's likely handled in global script) -->
<script src="js/modules/content-pages.js" defer></script>

<?php include '../includes/footer.php'; ?>
