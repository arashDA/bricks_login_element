<?php
/**
 * Plugin Name: Otp Login for Bricks
 * Description: OTP login for Bricks builder.
 * Version: 1.1.0
 * Author: Arash Dadjoo
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Load email OTP logic 
require_once plugin_dir_path(__FILE__) . 'includes/email.php';
// Load SMS sender registry and implementations
require_once plugin_dir_path(__FILE__) . 'includes/sms-senders.php';

// Async hook for sending email OTP without blocking AJAX request
add_action('login_send_email_otp_async', function($email, $otp) {
    // Wrap in try-catch to prevent errors from blocking the hook
    try {
        login_send_email_otp($email, $otp);
    } catch (Exception $e) {
        // Log error silently - email failure shouldn't break login flow
        error_log('OTP Email send failed: ' . $e->getMessage());
    }
}, 10, 2);

/*========================================================
  1. Activation: create OTP table
========================================================*/
register_activation_hook(__FILE__, function(){
    global $wpdb;
    $table = $wpdb->prefix.'login_otp';
    $charset = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table (
        phone varchar(20) NOT NULL,
        code varchar(10) NOT NULL,
        expires datetime NOT NULL,
        verified tinyint(1) DEFAULT 0,
        PRIMARY KEY (phone)
    ) $charset;";

    require_once ABSPATH.'wp-admin/includes/upgrade.php';
    dbDelta($sql);
});


// Also enqueue assets inside the Bricks editor preview so scripts run in the builder
add_action('admin_enqueue_scripts', function($hook) {
    // Try to detect Bricks editor preview/editor. Bricks typically uses `bricks_action=edit`
    // or includes a preview param. If not present, do not enqueue in all admin pages.
    $is_bricks_editor = ( isset($_GET['bricks_action']) && in_array($_GET['bricks_action'], ['edit','render','preview'], true) )
                      || isset($_GET['bricks_preview'])
                      || ( defined('DOING_AJAX') && DOING_AJAX && isset($_REQUEST['bricks_action']) );

    if ( ! $is_bricks_editor ) {
        return;
    }

    $base_url  = plugin_dir_url( __FILE__ );
    $base_path = plugin_dir_path( __FILE__ );

    // Register and enqueue style for editor preview
    wp_register_style(
        'login-otp-style',
        $base_url . 'assets/css/login.css',
        [],
        file_exists( $base_path . 'assets/css/login.css' ) ? filemtime( $base_path . 'assets/css/login.css' ) : null
    );
    wp_enqueue_style('login-otp-style');

    // Register and enqueue script for editor preview
    wp_register_script(
        'login-otp-script',
        $base_url . 'assets/js/login.js',
        [],
        file_exists( $base_path . 'assets/js/login.js' ) ? filemtime( $base_path . 'assets/js/login.js' ) : null,
        true
    );

    $icon_url = plugin_dir_url( __FILE__ );

    wp_localize_script( 'login-otp-script', 'loginOtpData', [
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'login_otp_nonce' ),
        'countdown' => intval( get_option( 'otp_login_countdown', 120 ) ),
        'otpLength' => intval( get_option( 'otp_login_otp_length', 6 ) ),
        'redirect' => esc_url( get_option( 'otp_login_redirect', home_url('/') ) ),
        'iconDanger' => $icon_url . 'assets/img/errorIcon.svg',
        'iconNotice' => $icon_url . 'assets/img/noticeIcon.svg',
        'iconSuccess' => $icon_url . 'assets/img/successIcon.svg',
    ]);

    wp_enqueue_script('login-otp-script');
});

/*========================================================
  2. Helpers
========================================================*/
function login_normalize_phone($phone){
    // Remove all non-digits
    $phone = preg_replace('/\D/', '', $phone);

    // Remove leading country code variations
    if (substr($phone, 0, 4) === '0098') {
        $phone = substr($phone, 4);
    }
    if (substr($phone, 0, 3) === '098') {
        $phone = substr($phone, 3);
    }
    if (substr($phone, 0, 2) === '98') {
        $phone = substr($phone, 2);
    }
    if (substr($phone, 0, 1) === '0') {
        $phone = substr($phone, 1);
    }

    // Now phone MUST be 10 digits
    return $phone;
}

/**
 * Lookup users by phone meta (supports both '0' prefixed and 10-digit)
 * or by user_login (username). Returns array of WP_User or empty array.
 */
function login_get_user_by_phone_or_username($input){
    // Accept username or phone-like input
    $norm = login_normalize_phone($input);

    // try meta stored as plain 10-digit
    $users = get_users([
        'meta_key'   => 'phone',
        'meta_value' => $norm,
        'number'     => 1
    ]);
    if (!empty($users)) return $users;

    // try meta stored with leading zero (admin saved format)
    $users = get_users([
        'meta_key'   => 'phone',
        'meta_value' => '0' . $norm,
        'number'     => 1
    ]);
    if (!empty($users)) return $users;

    // try user_login variants: raw input, normalized, leading zero
    $candidates = [ $input, $norm, '0' . $norm ];
    foreach ($candidates as $cand) {
        if (!$cand) continue;
        $u = get_user_by('login', $cand);
        if ($u) return [ $u ];
    }

    return [];
}


/*========================================================
  3. login SMS Sender
  Replace template ID and API key usage as needed
========================================================*/
// SMS sending is delegated to includes/sms-senders.php (provider-based)


/*========================================================
  4. OTP Logic
========================================================*/
function login_send_otp($input){
    global $wpdb;

    $table = $wpdb->prefix . 'login_otp';

    // Get OTP length from admin settings
    $otp_length = intval(get_option('otp_login_otp_length', 6));

    // Generate OTP with correct length
    $min = (int) pow(10, $otp_length - 1);
    $max = (int) pow(10, $otp_length) - 1;
    $otp = wp_rand($min, $max);

    // Get countdown from admin settings
    $countdown = intval(get_option('otp_login_countdown', 120));
    if ($countdown < 10) {
        $countdown = 120;
    }

    $expires = date('Y-m-d H:i:s', time() + $countdown);

    /*-----------------------------------------
     * Normalize phone
     *----------------------------------------*/
    $phone = login_normalize_phone($input);

    if (strlen($phone) !== 10) {
        return new WP_Error('invalid_phone', 'شماره تلفن نامعتبر است');
    }

    /*-----------------------------------------
     * Save OTP in DB
     *----------------------------------------*/
    $result = $wpdb->replace(
        $table,
        [
            'phone'    => $phone,
            'code'     => (string)$otp,
            'expires'  => $expires,
            'verified' => 0
        ],
        ['%s', '%s', '%s', '%d']
    );


    /*-----------------------------------------
     * Send SMS OTP (always) - delegate to provider configured in settings
     *----------------------------------------*/
    $sms = login_send_sms($phone, null, [$otp]);

    if (is_wp_error($sms)) {
        return $sms;
    }

    /*-----------------------------------------
     * If user exists AND has email → send email OTP too (async)
     *----------------------------------------*/
    $users = login_get_user_by_phone_or_username($phone);

    if (!empty($users)) {
        $user = $users[0];
        $email = $user->user_email;

        if ($email) {
            do_action( 'login_send_email_otp_async', $email, $otp );
        }

    }
    return true;
}





function login_verify_otp($phone, $code){
    global $wpdb;
    $phone = login_normalize_phone($phone);

    $row = $wpdb->get_row(
        $wpdb->prepare(
            "SELECT * FROM {$wpdb->prefix}login_otp 
            WHERE phone=%s AND code=%s",
            $phone, $code
        )
    );

    if (!$row) return false;

    // Compare expiry in PHP (correct timezone)
    if (strtotime($row->expires) < time()) {
        return false;
    }


    $wpdb->update($wpdb->prefix.'login_otp', ['verified'=>1], ['phone'=>$phone]);
    return true;
}





/*========================================================
  6. AJAX Handlers with nonce and basic validation
========================================================*/
add_action('wp_ajax_login_send_otp','login_ajax_send_otp');
add_action('wp_ajax_nopriv_login_send_otp','login_ajax_send_otp');

function login_ajax_send_otp(){
    check_ajax_referer('login_otp_nonce');

    $phone = sanitize_text_field($_POST['phone'] ?? '');

    if (!$phone) wp_send_json_error(['message'=>'شماره تلفن لازم است']);

    // Delegate to provider-based sender (credentials read from settings)
    $res = login_send_otp($phone);

    if (is_wp_error($res)) {
        wp_send_json_error(['message'=>$res->get_error_message()]);
    }

    wp_send_json_success([ 'message' => 'کد تایید ارسال شد' ]);
}


// GET USER INFO BY PHONE (name, email, etc)
add_action('wp_ajax_login_get_user_info','login_ajax_get_user_info');
add_action('wp_ajax_nopriv_login_get_user_info','login_ajax_get_user_info');

function login_ajax_get_user_info(){
    check_ajax_referer('login_otp_nonce');

    $phone = sanitize_text_field($_POST['phone'] ?? '');

    if (!$phone) wp_send_json_error(['message'=>'شماره تلفن لازم است']);

    $norm = login_normalize_phone($phone);

    $users = login_get_user_by_phone_or_username($norm);

    // If user exists, return their info
    if (!empty($users)) {
        $user = $users[0];
        wp_send_json_success([
            'exists' => true,
            'email'  => $user->user_email,
            'phone'  => $norm
        ]);
    }

    // User doesn't exist yet, just return the phone
    wp_send_json_success([
        'exists' => false,
        'email'  => null,
        'phone'  => $norm
    ]);
}

// CHECK PHONE EXISTS (lightweight) for forgot flow
add_action('wp_ajax_login_check_phone','login_ajax_check_phone');
add_action('wp_ajax_nopriv_login_check_phone','login_ajax_check_phone');

function login_ajax_check_phone(){
    check_ajax_referer('login_otp_nonce');

    $phone = sanitize_text_field($_POST['phone'] ?? '');

    if (!$phone) wp_send_json_error(['message'=>'شماره تلفن لازم است']);

    $norm = login_normalize_phone($phone);

    $users = login_get_user_by_phone_or_username($norm);

    if (empty($users)) {
        wp_send_json_error(['message'=>'Invalid user']);
    }

    wp_send_json_success(['exists' => true]);
}


// SEND OTP FOR FORGOT PASSWORD: only if user exists
add_action('wp_ajax_login_send_otp_forgot','login_ajax_send_otp_forgot');
add_action('wp_ajax_nopriv_login_send_otp_forgot','login_ajax_send_otp_forgot');

function login_ajax_send_otp_forgot(){
    check_ajax_referer('login_otp_nonce');

    $phone = sanitize_text_field($_POST['phone'] ?? '');

    if (!$phone) wp_send_json_error(['message'=>'Phone number is required ']);

    $norm = login_normalize_phone($phone);

    // Check if user exists for this phone
    $users = login_get_user_by_phone_or_username($norm);

    if (empty($users)) {
        wp_send_json_error(['message'=>'No account found with this phone number']);
    }

    // Delegate to provider-based sender (credentials read from settings)
    $res = login_send_otp($phone);

    if (is_wp_error($res)) {
        wp_send_json_error(['message'=>$res->get_error_message()]);
    }



    if (!empty($users)) {
        $user = $users[0];
        wp_send_json_success([
            'message' => 'Otp code send',
            'email'  => $user->user_email,
            'phone'  => $norm
        ]);
    }
}


add_action('wp_ajax_login_verify_otp','login_ajax_verify_otp');
add_action('wp_ajax_nopriv_login_verify_otp','login_ajax_verify_otp');

function login_ajax_verify_otp(){
    check_ajax_referer('login_otp_nonce');

    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $otp   = sanitize_text_field($_POST['otp'] ?? '');

    if (!$phone || !$otp) {
        wp_send_json_error(['message'=>'Phone and OTP required']);
    }

    if (!login_verify_otp($phone, $otp)) {
        wp_send_json_error(['message'=>'Invalid or expired OTP']);
    }

    // Check if user exists
    $users = login_get_user_by_phone_or_username(login_normalize_phone($phone));

    if (!empty($users)) {
        // User exists → login
        $user_id = $users[0]->ID;
        wp_set_current_user($user_id);
        wp_set_auth_cookie($user_id);

        wp_send_json_success([
            'message' => 'Logged in',
            'status'  => 'logged_in',
            'redirect' => esc_url(isset($_COOKIE['otp_prev_page']) ? $_COOKIE['otp_prev_page'] : home_url('/'))
        ]);
    }

    // User does NOT exist → ask for password
    wp_send_json_success([
        'message' => 'OTP verified, password required',
        'status'  => 'need_password',
        'redirect' => esc_url(isset($_COOKIE['otp_prev_page']) ? $_COOKIE['otp_prev_page'] : home_url('/'))
    ]);
}


add_action('wp_ajax_login_register_user','login_ajax_register_user');
add_action('wp_ajax_nopriv_login_register_user','login_ajax_register_user');

function login_ajax_register_user(){
    check_ajax_referer('login_otp_nonce');

    $phone    = sanitize_text_field($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$phone || !$password) {
        wp_send_json_error(['message'=>'Phone and password required']);
    }

    $phone = login_normalize_phone($phone);

    // Build a temporary email required by WP at creation time
    $temp_email = $phone . '@otpPlugin.com';

    // Create user with a system-generated password (or use $password if you prefer)
    $user_data = [
        'user_login' => $phone,
        'user_pass'  => wp_generate_password(), // system fake password
        'user_email' => $temp_email,
    ];

    $user_id = wp_insert_user($user_data);

    if (is_wp_error($user_id)) {
        wp_send_json_error(['message' => $user_id->get_error_message()]);
    }

    // Now we can set the real password the user provided (optional)
    wp_set_password($password, $user_id);

    // Persist phone meta
    update_user_meta($user_id, 'phone', $phone);

    // Mark mobile_verified if transient exists (as implemented earlier)
    $norm = login_normalize_phone($phone);
    if (get_transient('otp_verified_' . $norm)) {
        update_user_meta($user_id, 'mobile_verified', 1);
        delete_transient('otp_verified_' . $norm);
    } else {
        update_user_meta($user_id, 'mobile_verified', 0);
    }

    // Replace the stored email with an empty value (or a random placeholder)
    // Use empty string to hide everywhere; or use a non-unique placeholder:
    wp_update_user([
        'ID'         => $user_id,
        'user_email' => ''
    ]);

    // Log in the user
    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);

    wp_send_json_success([
        'message'  => 'Registered and logged in',
        'status'   => 'registered',
        'redirect' => esc_url(isset($_COOKIE['otp_prev_page']) ? $_COOKIE['otp_prev_page'] : home_url('/'))
    ]);
}

// PASSWORD LOGIN - verify phone and password directly
add_action('wp_ajax_login_verify_password','login_ajax_verify_password');
add_action('wp_ajax_nopriv_login_verify_password','login_ajax_verify_password');

function login_ajax_verify_password(){
    check_ajax_referer('login_otp_nonce');

    $phone    = sanitize_text_field($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? ''; 

    if (!$phone || !$password) {
        wp_send_json_error(['message'=>'Phone and password required']);
    }

    $phone = login_normalize_phone($phone);

    // Get user by phone
    $users = login_get_user_by_phone_or_username($phone);

    if (empty($users)) {
        wp_send_json_error(['message'=>'Invalid password or phone number']);
    }

    $user = $users[0];

    
    // Verify password
    $password_check = wp_check_password($password, $user->user_pass);
    
    if (!$password_check) {
        wp_send_json_error(['message'=>'Invalid password or phone number']);
    }

    // Password is correct, log the user in
    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID);

    wp_send_json_success([
        'message' => 'Logged in successfully',
        'status'  => 'logged_in',
        'redirect' => esc_url(isset($_COOKIE['otp_prev_page']) ? $_COOKIE['otp_prev_page'] : home_url('/'))
    ]);
}

// forgot password OTP verification

add_action('wp_ajax_login_forgot_verify_otp','login_ajax_forgot_verify_otp');
add_action('wp_ajax_nopriv_login_forgot_verify_otp','login_ajax_forgot_verify_otp');

function login_ajax_forgot_verify_otp(){
    check_ajax_referer('login_otp_nonce');

    $phone = sanitize_text_field($_POST['phone'] ?? '');
    $otp   = sanitize_text_field($_POST['otp'] ?? '');
    
    if (!$phone || !$otp) {
        wp_send_json_error(['message'=>'Phone and OTP required' , 'phone'=>$phone, 'otp'=>$otp]);
    }

    if (!login_verify_otp($phone, $otp)) {
        wp_send_json_error(['message'=>'Invalid or expired OTP']);
    }

    // Check if user exists (use helper which handles multiple stored formats)
    $users = login_get_user_by_phone_or_username($phone);

    if (empty($users)) {
        wp_send_json_error(['message'=>'No account found with this phone number']);
    }

    wp_send_json_success([
        'message' => 'OTP verified. Set new password.',
        'status'  => 'reset_password'
    ]);
}

// reset password handler

add_action('wp_ajax_login_reset_password','login_ajax_reset_password');
add_action('wp_ajax_nopriv_login_reset_password','login_ajax_reset_password');

function login_ajax_reset_password(){
    check_ajax_referer('login_otp_nonce');

    $phone    = sanitize_text_field($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? ''; 

    if (!$phone || !$password) {
        wp_send_json_error(['message'=>'Phone and password required']);
    }

    $phone = login_normalize_phone($phone);

    $users = login_get_user_by_phone_or_username($phone);

    if (empty($users)) {
        wp_send_json_error(['message'=>'User not found']);
    }

    $user_id = $users[0]->ID;

    wp_set_password($password, $user_id);

    wp_send_json_success([
        'message' => 'Password changed successfully',
        'status'  => 'password_changed'
    ]);
}





/*========================================================
  7. Register Bricks element file and assets
========================================================*/
add_action( 'init', function() {
    // Load element class
    if ( class_exists( '\Bricks\Elements' ) ) {
        // Register element file from plugin
        \Bricks\Elements::register_element( plugin_dir_path( __FILE__ ) . 'bricks/elements/login.php' );
    }
}, 11 );



add_action( 'wp_enqueue_scripts', function() {

    $base_url  = plugin_dir_url( __FILE__ );
    $base_path = plugin_dir_path( __FILE__ );

    wp_register_style(
        'login-otp-style',
        $base_url . 'assets/css/login.css',
        [],
        file_exists( $base_path . 'assets/css/login.css' ) ? filemtime( $base_path . 'assets/css/login.css' ) : null
    );

    // Ensure the style is enqueued on the frontend so Bricks preview or pages load it
    wp_enqueue_style('login-otp-style');

    wp_register_script(
        'login-otp-script',
        $base_url . 'assets/js/login.js',
        [],
        file_exists( $base_path . 'assets/js/login.js' ) ? filemtime( $base_path . 'assets/js/login.js' ) : null,
        true
    );

    wp_localize_script( 'login-otp-script', 'loginOtpData', [
        'ajaxUrl' => admin_url( 'admin-ajax.php' ),
        'nonce'   => wp_create_nonce( 'login_otp_nonce' ),
        'countdown' => intval( get_option( 'otp_login_countdown', 120 ) ),
        'otpLength' => intval( get_option( 'otp_login_otp_length', 6 ) ),
        'redirect' => esc_url( get_option( 'otp_login_redirect', home_url('/') ) ),
        'iconDanger' => $base_url . 'assets/img/errorIcon.svg',
        'iconNotice' => $base_url . 'assets/img/noticeIcon.svg',
        'iconSuccess' => $base_url . 'assets/img/successIcon.svg',
    ]);


});



// set the coockie for redirect after login
add_action('init', function () {
    if (!is_user_logged_in()){
        setcookie('otp_prev_page', esc_url_raw($_SERVER['REQUEST_URI']), time() + 300, '/');
    }
});

// Redirect users to configured URL after logout
add_action('wp_logout', function () {

    $logout_url = get_option('otp_login_logout_redirect');

    if (empty($logout_url)) {
        $logout_url = home_url('/');
    }

    wp_safe_redirect(esc_url_raw($logout_url));
    exit;

});

// redirect user to my-account if they are logined in and try to access login page
add_action( 'template_redirect', function() {

    if ( is_page( 'login' ) ) {

        // If user is logged in
        if ( is_user_logged_in() ) {

            // Check query parameters
            $forgot     = isset($_GET['forgot']) ? $_GET['forgot'] : null;
            $return_to  = isset($_GET['return_to']) ? $_GET['return_to'] : null;

            // If special forgot URL exists → allow access
            if ( $forgot === '1' && $return_to ) {
                return; // do NOT redirect
            }

            // Otherwise redirect normally
            wp_redirect( home_url( '/my-account' ) );
            exit;
        }
    }

});




// admin page for settings could be added here
add_action('admin_menu', function () {
    add_menu_page(
        'OTP Login Settings',
        'OTP Login',
        'manage_options',
        'otp-login-settings',
        'otp_login_settings_page',
        'dashicons-shield-alt',
        58
    );
});


add_action('admin_init', function () {
    register_setting('otp_login_settings', 'otp_login_redirect');
    register_setting('otp_login_settings', 'otp_login_countdown');
    register_setting('otp_login_settings', 'otp_login_otp_length');
    register_setting('otp_login_settings', 'otp_login_logout_redirect');

        // SMS provider selection and provider-specific credentials
        register_setting('otp_login_settings', 'otp_sms_provider');
        register_setting('otp_login_settings', 'otp_sms_melipayamak_username');
        register_setting('otp_login_settings', 'otp_sms_melipayamak_password');
        register_setting('otp_login_settings', 'otp_sms_melipayamak_template_id');
        register_setting('otp_login_settings', 'otp_sms_msgway_api_key');
        register_setting('otp_login_settings', 'otp_sms_msgway_sender');
});

// Enqueue admin JS for settings page to show/hide provider-specific fields
add_action('admin_enqueue_scripts', function($hook) {
    if ($hook !== 'toplevel_page_otp-login-settings') return;

    $base_url  = plugin_dir_url( __FILE__ );
    $base_path = plugin_dir_path( __FILE__ );

    wp_register_script(
        'otp-login-admin',
        $base_url . 'assets/js/admin-settings.js',
        ['jquery'],
        file_exists( $base_path . 'assets/js/admin-settings.js' ) ? filemtime( $base_path . 'assets/js/admin-settings.js' ) : null,
        true
    );

    wp_register_style(
        'otp-login-admin',
        $base_url . 'assets/css/admin-setting.css',
        [],
        file_exists( $base_path . 'assets/css/admin-setting.css' ) ? filemtime( $base_path . 'assets/css/admin-setting.css' ) : null
    );

    wp_enqueue_style('otp-login-admin');
    wp_enqueue_script('otp-login-admin');
    wp_localize_script('otp-login-admin', 'otpLoginAdminData', [
        'selected' => get_option('otp_sms_provider','melipayamak')
    ]);
});

/*========================================================
  8. User profile: add phone contact method and save handler
========================================================*/
// Add `phone` to Contact Info in user profile
add_filter('user_contactmethods', function($methods){
    $methods['phone'] = __('شماره موبایل','otp-login');
    return $methods;
});

// Save phone when profile is updated (both frontend and admin)
add_action('personal_options_update', 'otp_save_user_phone');
add_action('edit_user_profile_update', 'otp_save_user_phone');

function otp_save_user_phone($user_id){
    if (!current_user_can('edit_user', $user_id)) return false;

    if (!isset($_POST['phone'])) return false;

    $phone = sanitize_text_field($_POST['phone']);
    // Normalize and store as 10-digit local number (same format used elsewhere)
    $phone = login_normalize_phone($phone);

    if (empty($phone)) {
        delete_user_meta($user_id, 'phone');
    } else {
        update_user_meta($user_id, 'phone', '0' . $phone);
    }

    return true;
}

// Ensure phone input in user profile shows with a leading zero
add_action('show_user_profile', 'otp_ensure_phone_shows_zero');
add_action('edit_user_profile', 'otp_ensure_phone_shows_zero');
function otp_ensure_phone_shows_zero($user){
    ?>
    <script>
    (function(){
        document.addEventListener('DOMContentLoaded', function(){
            var el = document.querySelector('input[name="phone"]');
            if (!el) return;

            var raw = (el.value || '').toString();
            // keep only digits
            var digits = raw.replace(/\D/g, '');

            // If we have 10 digits (e.g. 9123456789) prepend a 0
            if (digits.length === 10 && digits.charAt(0) !== '0') {
                el.value = '0' + digits;
                return;
            }

            // If it's 11 digits but missing leading 0 (unlikely), ensure proper formatting
            if (digits.length === 11 && digits.charAt(0) !== '0') {
                el.value = '0' + digits.slice(-10);
                return;
            }

            // Otherwise leave the value as-is (covers already-correct values or empty)
        });
    })();
    </script>
    <?php
}



/*========================================================
  9. Bricks Dynamic tag (Phone number)
   - This can be used to display the user's phone number in the builder
========================================================*/
add_filter( 'bricks/dynamic_tags_list', 'myplugin_add_user_phone_tag' );
function myplugin_add_user_phone_tag( $tags ) {

  $tags[] = [
    'name'  => '{my_user_phone}',
    'label' => 'Current User Phone',
    'group' => 'My Plugin',
  ];

  return $tags;
}

add_filter( 'bricks/dynamic_data/render_tag', 'myplugin_render_user_phone_tag', 20, 3 );
function myplugin_render_user_phone_tag( $tag, $post, $context = 'text' ) {

  if ( ! is_string( $tag ) ) {
    return $tag;
  }

  $clean_tag = str_replace( ['{','}'], '', $tag );

  if ( $clean_tag !== 'my_user_phone' ) {
    return $tag;
  }

  return myplugin_get_user_phone();
}

function myplugin_get_user_phone() {

  $user = wp_get_current_user();

  if ( ! $user || ! $user->ID ) {
    return '';
  }

  $phone = get_user_meta( $user->ID, 'phone', true );

  if ( empty( $phone ) ) {
    return '';
  }

  // Ensure string
  $phone = (string) $phone;

  // Add leading zero if missing
  if ( $phone[0] !== '0' ) {
    $phone = '0' . $phone;
  }

  return esc_html( $phone );
}


add_filter( 'bricks/dynamic_data/render_content', 'myplugin_render_user_phone_content', 20, 3 );
add_filter( 'bricks/frontend/render_data', 'myplugin_render_user_phone_content', 20, 2 );

function myplugin_render_user_phone_content( $content, $post, $context = 'text' ) {

  if ( strpos( $content, '{my_user_phone}' ) === false ) {
    return $content;
  }

  return str_replace( '{my_user_phone}', myplugin_get_user_phone(), $content );
}





function otp_login_settings_page() {
    ?>
        <div class="otp-settings-wrapper">

            <?php if ( isset( $_GET['settings-updated'] ) ) : ?>
                <div class="otp-notice success">
                    <p><?php echo esc_html__( 'Settings saved successfully.', 'otp-login' ); ?></p>
                </div>
            <?php endif; ?>

            <h1 class="otp-title">OTP Login Settings</h1>

            <form method="post" action="options.php" class="otp-form">
                <div class="otp-settings-container">
                    <?php settings_fields('otp_login_settings'); ?>

                    <!-- General Settings Card -->
                    <div class="otp-card general-settings">
                        <h2>General Settings</h2>

                        <div class="otp-field">
                            <label>Redirect After Login</label>
                            <input type="text"
                                    name="otp_login_redirect"
                                    value="<?php echo esc_attr(get_option('otp_login_redirect', home_url('/'))); ?>">
                        </div>

                        <div class="otp-field">
                            <label>Redirect After Logout</label>
                            <input type="text"
                                    name="otp_login_logout_redirect"
                                    value="<?php echo esc_attr(get_option('otp_login_logout_redirect', '')); ?>">
                        </div>

                        <div class="otp-row">
                            <div class="otp-field small">
                                <label>Resend Countdown (seconds)</label>
                                <input type="number"
                                        name="otp_login_countdown"
                                        value="<?php echo esc_attr(get_option('otp_login_countdown', 120)); ?>">
                            </div>

                            <div class="otp-field small">
                                <label>OTP Length</label>
                                <input type="number"
                                        name="otp_login_otp_length"
                                        value="<?php echo esc_attr(get_option('otp_login_otp_length', 6)); ?>">
                            </div>
                        </div>
                    </div>

                    <!-- SMS Provider Card -->
                    <div class="otp-card">
                        <h2>SMS Provider</h2>

                        <div class="otp-field">
                            <label>Select Provider</label>
                            <select name="otp_sms_provider" id="otp_sms_provider">
                                <option value="melipayamak" <?php selected(get_option('otp_sms_provider','melipayamak'), 'melipayamak'); ?>>MeliPayamak</option>
                                <option value="msgway" <?php selected(get_option('otp_sms_provider','melipayamak'), 'msgway'); ?>>MSGWay</option>
                                <option value="smsir" <?php selected(get_option('otp_sms_provider','melipayamak'), 'smsir'); ?>>SMS.ir</option>
                                <option value="kavenegar" <?php selected(get_option('otp_sms_provider','melipayamak'), 'kavenegar'); ?>>Kavenegar</option>
                                <option value="ippannel" <?php selected(get_option('otp_sms_provider','melipayamak'), 'ippannel'); ?>>IPPannel</option>
                                <option value="none" <?php selected(get_option('otp_sms_provider','melipayamak'), 'none'); ?>>Disabled</option>
                            </select>
                        </div>

                        <!-- Melipayamak -->
                        <div class="provider-fields" data-provider="melipayamak">
                            <div class="otp-field">
                                <label>Melipayamak Username</label>
                                <input type="text"
                                        name="otp_sms_melipayamak_username"
                                        value="<?php echo esc_attr(get_option('otp_sms_melipayamak_username')); ?>">
                            </div>

                            <div class="otp-field">
                                <label>Melipayamak Password</label>
                                <input type="password"
                                        name="otp_sms_melipayamak_password"
                                        value="<?php echo esc_attr(get_option('otp_sms_melipayamak_password')); ?>">
                            </div>

                            <div class="otp-field small">
                                <label>Template ID</label>
                                <input type="number"
                                        name="otp_sms_melipayamak_template_id"
                                        value="<?php echo esc_attr(get_option('otp_sms_melipayamak_template_id')); ?>">
                            </div>
                        </div>

                        <!-- Msgway -->
                        <div class="provider-fields" data-provider="msgway">
                            <div class="otp-field">
                                <label>Msgway API Key</label>
                                <input type="text"
                                    name="otp_sms_msgway_api_key"
                                    value="<?php echo esc_attr(get_option('otp_sms_msgway_api_key')); ?>"
                                    autocomplete="off">
                                <p class="description">
                                    Your API key from Msgway dashboard.
                                </p>
                            </div>

                            <div class="otp-field">
                                <label>Msgway Template ID</label>
                                <input type="number"
                                    name="otp_sms_msgway_template_id"
                                    value="<?php echo esc_attr(get_option('otp_sms_msgway_template_id')); ?>"
                                    min="1">
                                <p class="description">
                                    Enter the template ID created in your Msgway panel.
                                </p>
                            </div>
                        </div>

                        <!-- Kavenegar -->
                        <div class="provider-fields" data-provider="kavenegar">

                            <div class="otp-field">
                                <label for="otp_sms_kavenegar_api_key">
                                    <strong>Kavenegar API Key</strong>
                                </label>
                                <input type="text"
                                    id="otp_sms_kavenegar_api_key"
                                    name="otp_sms_kavenegar_api_key"
                                    value="<?php echo esc_attr(get_option('otp_sms_kavenegar_api_key')); ?>"
                                    autocomplete="off">
                                <p class="description">
                                    Your API key from Kavenegar dashboard.
                                </p>
                            </div>

                            <div class="otp-field">
                                <label for="otp_sms_kavenegar_template_name">
                                    <strong>Kavenegar Template Name</strong>
                                </label>
                                <input type="text"
                                    id="otp_sms_kavenegar_template_name"
                                    name="otp_sms_kavenegar_template_name"
                                    value="<?php echo esc_attr(get_option('otp_sms_kavenegar_template_name')); ?>">
                                <p class="description">
                                    Enter the template name created in your Kavenegar panel.
                                </p>
                            </div>

                        </div>

                        <!-- SMS.IR -->
                        <div class="provider-fields" data-provider="smsir">

                            <div class="otp-field">
                                <label for="otp_sms_smsir_api_key">
                                    <strong>SMS.ir API Key</strong>
                                </label>
                                <input type="text"
                                    id="otp_sms_smsir_api_key"
                                    name="otp_sms_smsir_api_key"
                                    value="<?php echo esc_attr(get_option('otp_sms_smsir_api_key')); ?>"
                                    autocomplete="off">
                                <p class="description">
                                    Your API key from SMS.ir dashboard.
                                </p>
                            </div>

                            <div class="otp-field">
                                <label for="otp_sms_smsir_template_id">
                                    <strong>SMS.ir Template ID</strong>
                                </label>
                                <input type="text"
                                    id="otp_sms_smsir_template_id"
                                    name="otp_sms_smsir_template_id"
                                    value="<?php echo esc_attr(get_option('otp_sms_smsir_template_id')); ?>">
                                <p class="description">
                                    Enter the template ID created in your SMS.ir panel.
                                </p>
                            </div>

                        </div>

                        <!-- IPPannel -->
                        <div class="provider-fields" data-provider="ippannel">

                            <div class="otp-field">
                                <label for="otp_sms_ippannel_api_key">
                                    <strong>IPPannel API Key</strong>
                                </label>
                                <input type="text"
                                    id="otp_sms_ippannel_api_key"
                                    name="otp_sms_ippannel_api_key"
                                    value="<?php echo esc_attr(get_option('otp_sms_ippannel_api_key')); ?>"
                                    autocomplete="off">
                                <p class="description">
                                    Your API key from IPPannel dashboard.
                                </p>
                            </div>

                            <div class="otp-field">
                                <label for="otp_sms_ippannel_template_id">
                                    <strong>IPPannel Pattern Code</strong>
                                </label>
                                <input type="text"
                                    id="otp_sms_ippannel_template_id"
                                    name="otp_sms_ippannel_template_id"
                                    value="<?php echo esc_attr(get_option('otp_sms_ippannel_template_id')); ?>">
                                <p class="description">
                                    Enter the Pattern Code created in your IPPannel panel.
                                </p>
                            </div>

                            <div class="otp-field">
                                <label for="otp_sms_ippannel_originator">
                                    <strong>IPPannel Originator</strong>
                                </label>
                                <input type="text"
                                    id="otp_sms_ippannel_originator"
                                    name="otp_sms_ippannel_originator"
                                    value="<?php echo esc_attr(get_option('otp_sms_ippannel_originator')); ?>">
                                <p class="description">
                                    Enter the Originator created in your IPPannel panel.
                                </p>
                            </div>

                        </div>

                    </div>
                </div>

                <div class="otp-submit">
                    <?php submit_button('Save Settings'); ?>
                </div>
            </form>
        </div>
    <?php
}












