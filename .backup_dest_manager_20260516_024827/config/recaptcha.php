<?php
/**
 * Google reCAPTCHA configuration.
 *
 * These default keys are Google's official reCAPTCHA v2 test keys, which work
 * on localhost and always pass verification in development.
 *
 * Replace them with your own production keys before going live:
 * https://developers.google.com/recaptcha/docs/faq
 */

define('RECAPTCHA_SITE_KEY', '6LebcuosAAAAADDuMAZz6cqGYTc_nykD53sdH57A');
define('RECAPTCHA_SECRET_KEY', '6LebcuosAAAAAL4Oz_ShRWJ5AdGp4dwNq5fUjswz');

function recaptchaSiteKey() {
    return RECAPTCHA_SITE_KEY;
}

function recaptchaSecretKey() {
    return RECAPTCHA_SECRET_KEY;
}

function recaptchaIsConfigured() {
    return recaptchaSiteKey() !== '' && recaptchaSecretKey() !== '';
}
?>
