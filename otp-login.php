<?php
/**
 * Plugin Name: login OTP Login for Bricks
 * Description: OTP login via login and a Bricks element.
 * Version: 1.0
 * Author: Arash Dadjoo
 */

if ( ! defined( 'ABSPATH' ) ) exit;

// Load email OTP logic 
require_once plugin_dir_path(__FILE__) . 'includes/email.php';

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


/*========================================================
  3. login SMS Sender
  Replace template ID and API key usage as needed
========================================================*/
function login_send_sms($phone, $template_id, $params , $username, $password){


    if (!$username || !$password) {
        return new WP_Error('missing_credentials', 'نام کاربری / رمزعبور ملی پیامک تنظیم نشده است');
    }

    $phone = login_normalize_phone($phone);

    if (strlen($phone) !== 10) {
        return new WP_Error('invalid_phone','شماره تلفن نامعتبر است');
    }

    // Convert to full Iranian format
    $to = '0' . $phone;

    // Melipayamak SOAP API
    ini_set("soap.wsdl_cache_enabled","0");

    try {
        $client = new SoapClient(
            "http://api.payamak-panel.com/post/Send.asmx?wsdl",
            ["encoding" => "UTF-8"]
        );

        $data = [
            "username" => $username,
            "password" => $password,
            "to"       => $to,
            "text"     => $params,     // array of parameters
            "bodyId"   => $template_id // template ID
        ];

        $result = $client->SendByBaseNumber($data)->SendByBaseNumberResult;

        if ($result < 0) {
            return new WP_Error('sms_failed', 'Melipayamak error code: ' . $result);
        }

        return true;

    } catch (Exception $e) {
        return new WP_Error('sms_failed', $e->getMessage());
    }
}


/*========================================================
  4. OTP Logic
========================================================*/
function login_send_otp($input, $username, $password, $template_id){
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
     * Send SMS OTP (always)
     *----------------------------------------*/
    $sms = login_send_sms($phone, $template_id, [$otp], $username, $password);

    if (is_wp_error($sms)) {
        return $sms;
    }

    /*-----------------------------------------
     * If user exists AND has email → send email OTP too (async)
     *----------------------------------------*/
    $users = get_users([
        'meta_key'   => 'phone',
        'meta_value' => $phone,
        'number'     => 1
    ]);

    if (!empty($users)) {
        $user = $users[0];
        $email = $user->user_email;

        if (login_is_real_email($email)) {
            wp_schedule_single_event(time(), 'login_send_email_otp_async', [$email, $otp]);
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

    // Get SMS credentials from admin settings
    $username = get_option('otp_login_username');
    $password = get_option('otp_login_password');
    $template = get_option('otp_login_template_id');

    if (!$username || !$password || !$template) {
        wp_send_json_error(['message'=>'نام کاربری، رمزعبور و قالب پیامک در تنظیمات تعریف نشده است']);
    }

    $res = login_send_otp($phone, $username, $password, $template);

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

    $users = get_users([
        'meta_key'   => 'phone',
        'meta_value' => $norm,
        'number'     => 1
    ]);

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

    $users = get_users([
        'meta_key'   => 'phone',
        'meta_value' => $norm,
        'number'     => 1
    ]);

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

    if (!$phone) wp_send_json_error(['message'=>'شماره تلفن لازم است']);

    $norm = login_normalize_phone($phone);

    // Check if user exists for this phone
    $users = get_users([
        'meta_key'   => 'phone',
        'meta_value' => $norm,
        'number'     => 1
    ]);

    if (empty($users)) {
        wp_send_json_error(['message'=>'No account found with this phone number']);
    }

    // Get SMS credentials from admin settings
    $username = get_option('otp_login_username');
    $password = get_option('otp_login_password');
    $template = get_option('otp_login_template_id');

    if (!$username || !$password || !$template) {
        wp_send_json_error(['message'=>'نام کاربری، رمزعبور و قالب پیامک در تنظیمات تعریف نشده است']);
    }

    $res = login_send_otp($phone, $username, $password, $template);

    if (is_wp_error($res)) {
        wp_send_json_error(['message'=>$res->get_error_message()]);
    }

    wp_send_json_success([ 'message' => 'کد تایید ارسال شد' ]);
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
    $users = get_users([
        'meta_key'   => 'phone',
        'meta_value' => login_normalize_phone($phone),
        'number'     => 1
    ]);

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

    // Create user
    $username = $phone;
    $email    = $phone . '@OtpPlugin.com';

    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        wp_send_json_error(['message'=>$user_id->get_error_message()]);
    }

    error_log('User created with ID: ' . $user_id);

    update_user_meta($user_id, 'phone', $phone);

    wp_set_current_user($user_id);
    wp_set_auth_cookie($user_id);

    wp_send_json_success([
        'message' => 'Registered and logged in',
        'status'  => 'registered',
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
    $users = get_users([
        'meta_key'   => 'phone',
        'meta_value' => $phone,
        'number'     => 1
    ]);

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

    // Check if user exists
    $users = get_users([
        'meta_key'   => 'phone',
        'meta_value' => login_normalize_phone($phone),
        'number'     => 1
    ]);

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

    $users = get_users([
        'meta_key'   => 'phone',
        'meta_value' => $phone,
        'number'     => 1
    ]);

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

    wp_enqueue_script('login-otp-script');

});



// set the coockie for redirect after login
add_action('init', function () {
    if (!is_user_logged_in()){
        setcookie('otp_prev_page', esc_url_raw($_SERVER['REQUEST_URI']), time() + 300, '/');
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

    register_setting('otp_login_settings', 'otp_login_username');
    register_setting('otp_login_settings', 'otp_login_password');
    register_setting('otp_login_settings', 'otp_login_template_id');
});


// Email Helper
function login_is_real_email($email) {
    if (!$email || !is_email($email)) {
        return false;
    }

    // Block auto-generated emails
    if (str_ends_with($email, '@OtpPlugin.com')) {
        return false;
    }

    return true;
}


function otp_login_settings_page() {
    ?>
    <div class="wrap">
        <h1>OTP Login Settings</h1>

        <form method="post" action="options.php">
            <?php settings_fields('otp_login_settings'); ?>

            <table class="form-table">

                <tr>
                    <th>Redirect After Login</th>
                    <td>
                        <input type="text" name="otp_login_redirect"
                               value="<?php echo esc_attr(get_option('otp_login_redirect', home_url('/'))); ?>"
                               class="regular-text">
                    </td>
                </tr>

                <tr>
                    <th>Resend Countdown (seconds)</th>
                    <td>
                        <input type="number" name="otp_login_countdown"
                               value="<?php echo esc_attr(get_option('otp_login_countdown', 120)); ?>"
                               class="small-text">
                    </td>
                </tr>

                <tr>
                    <th>OTP Length</th>
                    <td>
                        <input type="number" name="otp_login_otp_length"
                               value="<?php echo esc_attr(get_option('otp_login_otp_length', 6)); ?>"
                               class="small-text">
                    </td>
                </tr>

                <tr>
                    <th>Melipayamak Username</th>
                    <td>
                        <input type="text" name="otp_login_username"
                               value="<?php echo esc_attr(get_option('otp_login_username')); ?>"
                               class="regular-text">
                    </td>
                </tr>

                <tr>
                    <th>Melipayamak Password</th>
                    <td>
                        <input type="password" name="otp_login_password"
                               value="<?php echo esc_attr(get_option('otp_login_password')); ?>"
                               class="regular-text">
                    </td>
                </tr>

                <tr>
                    <th>Melipayamak Template ID</th>
                    <td>
                        <input type="number" name="otp_login_template_id"
                               value="<?php echo esc_attr(get_option('otp_login_template_id')); ?>"
                               class="small-text">
                    </td>
                </tr>

            </table>

            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}












