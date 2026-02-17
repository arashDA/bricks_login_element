<?php
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Simple SMS sender registry with a built-in Melipayamak implementation.
 * Other senders can be added here or via filters.
 */

/**
 * Return the currently selected SMS provider key.
 */
function login_get_selected_sms_provider() {
    return get_option('otp_sms_provider', 'melipayamak');
}


/**
 * Main facade used by the plugin: keeps signature compatible with older calls.
 * Accepts: ($phone, $template_id, $params = [], $username = null, $password = null)
 */
function login_send_sms($phone, $template_id = null, $params = [], $username = null, $password = null) {
    $provider = login_get_selected_sms_provider();

    switch ($provider) {
        case 'melipayamak':
            // use provider-specific option template if not passed
            $template = $template_id ?: get_option('otp_sms_melipayamak_template_id');
            return login_sms_send_melipayamak($phone, $template, $params);

        case 'msgway':
            $api_key = get_option('otp_sms_msgway_api_key');
            $sender  = get_option('otp_sms_msgway_sender');
            return login_sms_send_msgway($phone, $api_key, $sender, $params);

        case 'none':
        default:
            return new WP_Error('no_provider', 'No SMS provider configured');
    }
}

/**
 * Melipayamak sender implementation.
 * Returns true on success or WP_Error on failure.
 */
function login_sms_send_melipayamak($phone, $template_id, $params = []) {
    if (!$template_id) {
        return new WP_Error('missing_template', 'Template ID not configured for Melipayamak');
    }

    $username = get_option('otp_sms_melipayamak_username');
    $password = get_option('otp_sms_melipayamak_password');

    if (!$username || !$password) {
        return new WP_Error('missing_credentials', 'Melipayamak credentials not configured');
    }

    // Normalize phone the same way main plugin does
    if (!function_exists('login_normalize_phone')) {
        return new WP_Error('missing_helper', 'Phone normalization helper missing');
    }

    $phone = login_normalize_phone($phone);

    if (strlen($phone) !== 10) {
        return new WP_Error('invalid_phone','شماره تلفن نامعتبر است');
    }

    $to = '0' . $phone;

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
            "text"     => $params,
            "bodyId"   => $template_id
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



/**
 * Msgway sender implementation.
 * Returns true on success or WP_Error on failure.
 */
function login_sms_send_msgway($phone, $api_key, $template_id, $template_params = []) {

    if (!$api_key) {
        return new WP_Error('missing_credentials', 'Msgway API key not configured');
    }

    if (!function_exists('login_normalize_phone')) {
        return new WP_Error('missing_helper', 'Phone normalization helper missing');
    }

    $phone = login_normalize_phone($phone);

    if (strlen($phone) !== 10) {
        return new WP_Error('invalid_phone','شماره تلفن نامعتبر است');
    }

    // Convert to international format
    $mobile = '+98' . $phone;

    $endpoint = 'https://api.msgway.com/send';

    $body = [
        'mobile'     => $mobile,
        'method'     => 'sms',
        'templateID' => (int) $template_id,
        'params'     => array_values((array) $template_params), // ensure indexed array
    ];

    $args = [
        'timeout' => 20,
        'headers' => [
            'Content-Type' => 'application/json',
            'apiKey'       => $api_key,
        ],
        'body' => wp_json_encode($body),
    ];

    $response = wp_remote_post($endpoint, $args);

    if (is_wp_error($response)) {
        return $response;
    }

    $http_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);

    if ($http_code < 200 || $http_code >= 300) {
        return new WP_Error('sms_failed', 'Msgway error: HTTP ' . $http_code . ' - ' . $response_body);
    }

    return true;
}



/**
 * Kavenegar sender implementation .
 * Returns true on success or WP_Error on failure.
 */
function login_sms_send_kavenegar($phone,$api_key,$template,$token,$token2 = null,$token3 = null,$type = 'sms') {

    if (!$api_key) {
        return new WP_Error('missing_credentials', 'Kavenegar API key not configured');
    }

    if (!function_exists('login_normalize_phone')) {
        return new WP_Error('missing_helper', 'Phone normalization helper missing');
    }

    $phone = login_normalize_phone($phone);

    if (strlen($phone) !== 10) {
        return new WP_Error('invalid_phone', 'شماره تلفن نامعتبر است');
    }

    // Kavenegar expects 09XXXXXXXXX format
    $receptor = '0' . $phone;

    $endpoint = "https://api.kavenegar.com/v1/{$api_key}/verify/lookup.json";

    $body = [
        'receptor' => $receptor,
        'token'    => $token,
        'template' => $template,
        'type'     => $type, // sms or call
    ];

    // Optional tokens
    if (!empty($token2)) {
        $body['token2'] = $token2;
    }

    if (!empty($token3)) {
        $body['token3'] = $token3;
    }

    $args = [
        'timeout' => 20,
        'body'    => $body,
    ];

    $response = wp_remote_post($endpoint, $args);

    if (is_wp_error($response)) {
        return $response;
    }

    $http_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);

    if ($http_code < 200 || $http_code >= 300) {
        return new WP_Error('sms_failed', 'Kavenegar HTTP Error: ' . $http_code);
    }

    $data = json_decode($response_body, true);

    if (!isset($data['return']['status']) || $data['return']['status'] != 200) {
        return new WP_Error(
            'sms_failed',
            'Kavenegar Error: ' . ($data['return']['message'] ?? 'Unknown error')
        );
    }

    return true;
}



/**
 * SMS.ir sender implementation .
 * Returns true on success or WP_Error on failure.
 */
function login_sms_send_smsir($phone,$api_key,$template_id,$parameters = []) {

    if (!$api_key) {
        return new WP_Error('missing_credentials', 'SMS.ir API key not configured');
    }

    if (!function_exists('login_normalize_phone')) {
        return new WP_Error('missing_helper', 'Phone normalization helper missing');
    }

    $phone = login_normalize_phone($phone);

    if (strlen($phone) !== 10) {
        return new WP_Error('invalid_phone', 'شماره تلفن نامعتبر است');
    }

    // SMS.ir expects 09XXXXXXXXX format
    $mobile = '0' . $phone;

    $endpoint = 'https://api.sms.ir/v1/send/verify';

    // Build parameters array properly
    $formatted_params = [];

    foreach ((array) $parameters as $name => $value) {
        $formatted_params[] = [
            'name'  => (string) $name,
            'value' => (string) $value,
        ];
    }

    $body = [
        'mobile'     => $mobile,
        'templateId' => (int) $template_id,
        'parameters' => $formatted_params,
    ];

    $args = [
        'timeout' => 20,
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept'       => 'text/plain',
            'x-api-key'    => $api_key,
        ],
        'body' => wp_json_encode($body),
    ];

    $response = wp_remote_post($endpoint, $args);

    if (is_wp_error($response)) {
        return $response;
    }

    $http_code = wp_remote_retrieve_response_code($response);
    $response_body = wp_remote_retrieve_body($response);

    if ($http_code < 200 || $http_code >= 300) {
        return new WP_Error('sms_failed', 'SMS.ir HTTP Error: ' . $http_code);
    }

    $data = json_decode($response_body, true);

    if (isset($data['status']) && $data['status'] != 1) {
        return new WP_Error('sms_failed', 'SMS.ir Error: ' . ($data['message'] ?? 'Unknown error'));
    }

    return true;
}




/**
 * Filter hook to allow registering custom sender callbacks externally.
 * Example: add_filter('login_sms_sender_{key}', function($phone,$template,$params){ ... }, 10, 3);
 */

?>