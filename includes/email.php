<?php

if (!defined('ABSPATH')) exit;

function login_send_email_otp($email, $otp) {

    $subject = "کد تایید ورود شما";
    $message = "کد تایید شما: $otp\nاین کد پس از 5 دقیقه منقضی می‌شود.";

    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        'From: Your Website <no-reply@yourwebsite.com>'
    ];

    $sent = wp_mail($email, $subject, $message, $headers);

    if (!$sent) {
        return new WP_Error('email_failed', 'ارسال ایمیل با خطا مواجه شد');
    }

    return true;
}
