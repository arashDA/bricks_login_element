<?php

if (!defined('ABSPATH')) exit;

function login_send_email_otp($email, $otp) {

    $subject = 'کد تأیید ورود شما';

    $message = '
    <html>
      <body style="direction:rtl;font-family:tahoma,arial">
        <p>کد تأیید شما:</p>
        <h2 style="letter-spacing:2px">'.$otp.'</h2>
        <p>این کد پس از <strong>۲ دقیقه</strong> منقضی می‌شود.</p>
      </body>
    </html>';

    $headers = [
        'Content-Type: text/html; charset=UTF-8',
        'From: Eghamat24 <no-reply@eghamat24.com>',
        'Reply-To: support@eghamat24.com'
    ];

    $sent = wp_mail($email, $subject, $message, $headers);

    if ( ! $sent ) {
        return new WP_Error('email_failed', 'ارسال ایمیل با خطا مواجه شد');
    }

    return true;
}

