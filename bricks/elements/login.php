<?php
if (!defined('ABSPATH')) exit;

class Wcoq_login_OTP extends \Bricks\Element {

    public $category = 'webcoq';
    public $name     = 'login';
    public $icon     = 'ti-mobile';
    public $scripts  = ['login-otp-script'];

    public function get_label() {
        return esc_html__('Login OTP', 'bricks');
    }


/*---------------------------------------------
 * CONTROL GROUPS
 *--------------------------------------------*/
public function set_control_groups() {
    $this->control_groups['Login_labels'] = [
        'title' => esc_html__('Login', 'bricks'),
        'tab'   => 'content',
    ];

    $this->control_groups['LoginPassword_labels'] = [
        'title' => esc_html__('Login with password', 'bricks'),
        'tab'   => 'content',
    ];

    $this->control_groups['LoginOTP_labels'] = [
        'title' => esc_html__('Login with OTP', 'bricks'),
        'tab'   => 'content',
    ];

    $this->control_groups['SetPassword_labels'] = [
        'title' => esc_html__('Set password page', 'bricks'),
        'tab'   => 'content',
    ];

    $this->control_groups['ForgetPassword_labels'] = [
        'title' => esc_html__('Forgot password page', 'bricks'),
        'tab'   => 'content',
    ];

    $this->control_groups['style_inputs'] = [
        'title' => esc_html__('Inputs', 'bricks'),
        'tab'   => 'style',
    ];

    $this->control_groups['style_buttons'] = [
        'title' => esc_html__('Buttons', 'bricks'),
        'tab'   => 'style',
    ];

    $this->control_groups['style_labels'] = [
        'title' => esc_html__('Labels', 'bricks'),
        'tab'   => 'style',
    ];

    $this->control_groups['style_resend'] = [
        'title' => esc_html__('Resend Link', 'bricks'),
        'tab'   => 'style',
    ];

    $this->control_groups['style_message'] = [
        'title' => esc_html__('Message Text', 'bricks'),
        'tab'   => 'style',
    ];

    $this->control_groups['style_title'] = [
        'title' => esc_html__('Title', 'bricks'),
        'tab' => 'style',
    ];

    $this->control_groups['style_subtitle'] = [
        'title' => esc_html__('Subtitle', 'bricks'),
        'tab' => 'style',
    ];

    $this->control_groups['style_loginWithPassword'] = [
        'title' => esc_html__('Login With Password', 'bricks'),
        'tab' => 'style',
    ];

    // Added missing groups
    $this->control_groups['typography'] = [
        'title' => esc_html__('Typography', 'bricks'),
        'tab' => 'style',
    ];

    $this->control_groups['background'] = [
        'title' => esc_html__('Background', 'bricks'),
        'tab' => 'style',
    ];

    // Step-specific button style groups
    $this->control_groups['style_login_button'] = [
        'title' => esc_html__('Login Button', 'bricks'),
        'tab'   => 'style',
    ];

    $this->control_groups['style_password_button'] = [
        'title' => esc_html__('Password Button', 'bricks'),
        'tab'   => 'style',
    ];

    $this->control_groups['style_otp_button'] = [
        'title' => esc_html__('OTP Button', 'bricks'),
        'tab'   => 'style',
    ];

    $this->control_groups['style_setpassword_button'] = [
        'title' => esc_html__('Set Password Button', 'bricks'),
        'tab'   => 'style',
    ];

    $this->control_groups['style_forgot_button'] = [
        'title' => esc_html__('Forgot Password Button', 'bricks'),
        'tab'   => 'style',
    ];

    // Step-specific input style groups
    $this->control_groups['style_login_input'] = [
        'title' => esc_html__('Login Input', 'bricks'),
        'tab'   => 'style',
    ];

    $this->control_groups['style_password_input'] = [
        'title' => esc_html__('Password Input', 'bricks'),
        'tab'   => 'style',
    ];

    $this->control_groups['style_otp_input'] = [
        'title' => esc_html__('OTP Input', 'bricks'),
        'tab'   => 'style',
    ];

    $this->control_groups['style_setpassword_input'] = [
        'title' => esc_html__('Set Password Input', 'bricks'),
        'tab'   => 'style',
    ];

    $this->control_groups['style_forgot_input'] = [
        'title' => esc_html__('Forgot Password Input', 'bricks'),
        'tab'   => 'style',
    ];

    // Step-specific label style groups
    $this->control_groups['style_login_label'] = [
        'title' => esc_html__('Login Label', 'bricks'),
        'tab'   => 'style',
    ];

    $this->control_groups['style_password_label'] = [
        'title' => esc_html__('Password Label', 'bricks'),
        'tab'   => 'style',
    ];

    $this->control_groups['style_otp_label'] = [
        'title' => esc_html__('OTP Label', 'bricks'),
        'tab'   => 'style',
    ];

    $this->control_groups['style_setpassword_label'] = [
        'title' => esc_html__('Set Password Label', 'bricks'),
        'tab'   => 'style',
    ];

    $this->control_groups['style_forgot_label'] = [
        'title' => esc_html__('Forgot Password Label', 'bricks'),
        'tab'   => 'style',
    ];

    $this->control_groups['style_timer_label'] = [
        'title' => esc_html__('Reset Timer', 'bricks'),
        'tab'   => 'style',
    ];

    $this->control_groups['style_login_password_error'] = [
        'title' => esc_html__('Login Password Error', 'bricks'),
        'tab'   => 'style',
    ];

    $this->control_groups['style_login_with_otp_button'] = [
        'title' => esc_html__('Login With Otp Button', 'bricks'),
        'tab'   => 'style',
    ];

    $this->control_groups['style_password_icon'] = [
        'title' => esc_html__('Password Icon', 'bricks'),
        'tab'   => 'style',
    ];

    $this->control_groups['style_country_code'] = [
        'title' => esc_html__('Country Code', 'bricks'),
        'tab'   => 'style',
    ];

    $this->control_groups['style_success_message_text'] = [
        'title' => esc_html__('Success Message Text', 'bricks'),
        'tab'   => 'style',
    ];

    $this->control_groups['style_error_message_text'] = [
        'title' => esc_html__('Error Message Text', 'bricks'),
        'tab'   => 'style',
    ];

    $this->control_groups['style_notice_message_text'] = [
        'title' => esc_html__('Notice Message Text', 'bricks'),
        'tab'   => 'style',
    ];



}

/*---------------------------------------------
 * CONTROLS
 *--------------------------------------------*/
public function set_controls() {

    /*-------------------------
     * LABELS & PLACEHOLDERS (Content Tab)
     *-------------------------*/
    // Login 
    $this->controls['login_title'] = [
        'tab'    => 'content',
        'group'  => 'Login_labels',
        'label'  => esc_html__('Login Title', 'bricks'),
        'type'   => 'text',
        'default'=> 'ورود'
    ];

    $this->controls['login_subtitle'] = [
        'tab'    => 'content',
        'group'  => 'Login_labels',
        'label'  => esc_html__('Login Subtitle', 'bricks'),
        'type'   => 'text',
        'default'=> 'توضیحات صفحه ورود'
    ];

    $this->controls['login_label'] = [
        'tab'    => 'content',
        'group'  => 'Login_labels',
        'label'  => esc_html__('Login Input Label', 'bricks'),
        'type'   => 'text',
        'default'=> ''
    ];

    $this->controls['login_placeholder'] = [
        'tab'    => 'content',
        'group'  => 'Login_labels',
        'label'  => esc_html__('Login Input Placeholder', 'bricks'),
        'type'   => 'text',
        'default'=> ''
    ];

    $this->controls['login_button_title'] = [
        'tab'    => 'content',
        'group'  => 'Login_labels',
        'label'  => esc_html__('Login Button text', 'bricks'),
        'type'   => 'text',
        'default'=> 'لاگین'
    ];


    $this->controls['login_switch_password'] = [
        'tab'    => 'content',
        'group'  => 'Login_labels',
        'label'  => esc_html__('Switch login with password', 'bricks'),
        'type'   => 'text',
        'default'=> 'ورود با رمزعبور'
    ];
    // Login with Password

    $this->controls['login_password_title'] = [
        'tab'    => 'content',
        'group'  => 'LoginPassword_labels',
        'label'  => esc_html__('Login Password Title', 'bricks'),
        'type'   => 'text',
        'default'=> 'ورود با رمز عبور'
    ];

    $this->controls['login_password_Subtitle'] = [
        'tab'    => 'content',
        'group'  => 'LoginPassword_labels',
        'label'  => esc_html__('Login Password Subtitle', 'bricks'),
        'type'   => 'text',
        'default'=> 'توضیحات ورود با رمزعبور'
    ];

    $this->controls['login_password_username_label'] = [
        'tab'    => 'content',
        'group'  => 'LoginPassword_labels',
        'label'  => esc_html__('Login password username Label', 'bricks'),
        'type'   => 'text',
        'default'=> ''
    ];

    $this->controls['login_password_username_placeholder'] = [
        'tab'    => 'content',
        'group'  => 'LoginPassword_labels',
        'label'  => esc_html__('Login password username Placeholder', 'bricks'),
        'type'   => 'text',
        'default'=> ''
    ];

    $this->controls['login_password_password_label'] = [
        'tab'    => 'content',
        'group'  => 'LoginPassword_labels',
        'label'  => esc_html__('Login password password Label', 'bricks'),
        'type'   => 'text',
        'default'=> ''
    ];

    $this->controls['login_password_password_placeholder'] = [
        'tab'    => 'content',
        'group'  => 'LoginPassword_labels',
        'label'  => esc_html__('Login password password Placeholder', 'bricks'),
        'type'   => 'text',
        'default'=> ''
    ];

    $this->controls['login_password_button_title'] = [
        'tab'    => 'content',
        'group'  => 'LoginPassword_labels',
        'label'  => esc_html__('Login password Button text', 'bricks'),
        'type'   => 'text',
        'default'=> 'لاگین'
    ];

    $this->controls['login_password_forgetPassword_title'] = [
        'tab'    => 'content',
        'group'  => 'LoginPassword_labels',
        'label'  => esc_html__('Login password forgetPassword Button text', 'bricks'),
        'type'   => 'text',
        'default'=> 'لاگین'
    ];

    $this->controls['login_password_loginOTP_title'] = [
        'tab'    => 'content',
        'group'  => 'LoginPassword_labels',
        'label'  => esc_html__('Login password LoginOTP Button text', 'bricks'),
        'type'   => 'text',
        'default'=> 'لاگین'
    ];

    // Login with OTP

    $this->controls['login_OTP_title'] = [
        'tab'    => 'content',
        'group'  => 'LoginOTP_labels',
        'label'  => esc_html__('Login OTP Title', 'bricks'),
        'type'   => 'text',
        'default'=> 'ارسال OTP'
    ];

    $this->controls['login_OTP_EditNumber'] = [
        'tab'    => 'content',
        'group'  => 'LoginOTP_labels',
        'label'  => esc_html__('Login OTP Edit Number', 'bricks'),
        'type'   => 'text',
        'default'=> 'ویرایش شماره'
    ];

    $this->controls['login_OTP_placeholder'] = [
        'tab'    => 'content',
        'group'  => 'LoginOTP_labels',
        'label'  => esc_html__('Login OTP Input Placeholder', 'bricks'),
        'type'   => 'text',
        'default'=> ''
    ];

    $this->controls['login_OTP_button_title'] = [
        'tab'    => 'content',
        'group'  => 'LoginOTP_labels',
        'label'  => esc_html__('Login OTP Button text', 'bricks'),
        'type'   => 'text',
        'default'=> 'ورود'
    ];

    $this->controls['login_OTP_resendCode'] = [
        'tab'    => 'content',
        'group'  => 'LoginOTP_labels',
        'label'  => esc_html__('Login OTP ResendButton text', 'bricks'),
        'type'   => 'text',
        'default'=> 'ورود'
    ];


    // Set password Page

    $this->controls['set_password_title'] = [
        'tab'    => 'content',
        'group'  => 'SetPassword_labels',
        'label'  => esc_html__('Set Password title', 'bricks'),
        'type'   => 'text',
        'default'=> 'تعریف رمز عبور'
    ];

    $this->controls['set_password_subTitle'] = [
        'tab'    => 'content',
        'group'  => 'SetPassword_labels',
        'label'  => esc_html__('Set Password Subtitle', 'bricks'),
        'type'   => 'text',
        'default'=> 'یک رمز عبور برای حساب کاربری خود انتخاب کنید. این رمز زمانی که به کد تایید دسترسی ندارید،برای ورود به حساب به شما کمک میکند.'
    ];

    $this->controls['set_password_label'] = [
        'tab'    => 'content',
        'group'  => 'SetPassword_labels',
        'label'  => esc_html__('Set password Input Label', 'bricks'),
        'type'   => 'text',
        'default'=> 'رمز عبور خود را وارد کنید'
    ];

    $this->controls['set_password_placeholder'] = [
        'tab'    => 'content',
        'group'  => 'SetPassword_labels',
        'label'  => esc_html__('Set password Input Placeholder', 'bricks'),
        'type'   => 'text',
        'default'=> ''
    ];

    $this->controls['set_password_button_title'] = [
        'tab'    => 'content',
        'group'  => 'SetPassword_labels',
        'label'  => esc_html__('Set password Button text', 'bricks'),
        'type'   => 'text',
        'default'=> 'ثبت نام'
    ];

    // Forget Password Page

    $this->controls['Forget_password_step1_title'] = [
        'tab'    => 'content',
        'group'  => 'ForgetPassword_labels',
        'label'  => esc_html__('Forget password Step 1 Title', 'bricks'),
        'type'   => 'text',
        'default'=> 'عنوان مرحله اول فراموشی رمز عبور'
    ];

    $this->controls['Forget_password_step1_subTitle'] = [
        'tab'    => 'content',
        'group'  => 'ForgetPassword_labels',
        'label'  => esc_html__('Forget password Step 1 subTitle', 'bricks'),
        'type'   => 'text',
        'default'=> 'زیر عنوان مرحله اول فراموشی رمز عبور'
    ];

    $this->controls['Forget_password_step1_label'] = [
        'tab'    => 'content',
        'group'  => 'ForgetPassword_labels',
        'label'  => esc_html__('Forget password Step 1 Input Label', 'bricks'),
        'type'   => 'text',
        'default'=> 'شماره موبایل خود را وارد کنید'
    ];

    $this->controls['Forget_password_step1_placeholder'] = [
        'tab'    => 'content',
        'group'  => 'ForgetPassword_labels',
        'label'  => esc_html__('Forget password Step 1 Input Placeholder', 'bricks'),
        'type'   => 'text',
        'default'=> ''
    ];

    $this->controls['Forget_password_step1_button_title'] = [
        'tab'    => 'content',
        'group'  => 'ForgetPassword_labels',
        'label'  => esc_html__('Forget password Step 1 Button text', 'bricks'),
        'type'   => 'text',
        'default'=> 'ارسال کد OTP'
    ];



    $this->controls['Forget_password_step2_title'] = [
        'tab'    => 'content',
        'group'  => 'ForgetPassword_labels',
        'label'  => esc_html__('Forget password Step 2 Title', 'bricks'),
        'type'   => 'text',
        'default'=> 'عنوان مرحله اول فراموشی رمز عبور'
    ];

    $this->controls['Forget_password_step2_subTitle'] = [
        'tab'    => 'content',
        'group'  => 'ForgetPassword_labels',
        'label'  => esc_html__('Forget password Step 2 subTitle', 'bricks'),
        'type'   => 'text',
        'default'=> 'زیر عنوان مرحله اول فراموشی رمز عبور'
    ];

    $this->controls['Forget_password_step2_label'] = [
        'tab'    => 'content',
        'group'  => 'ForgetPassword_labels',
        'label'  => esc_html__('Forget password Step 2 Input Label', 'bricks'),
        'type'   => 'text',
        'default'=> 'شماره موبایل خود را وارد کنید'
    ];

    $this->controls['Forget_password_step2_placeholder'] = [
        'tab'    => 'content',
        'group'  => 'ForgetPassword_labels',
        'label'  => esc_html__('Forget password Step 2 Input Placeholder', 'bricks'),
        'type'   => 'text',
        'default'=> ''
    ];

    $this->controls['Forget_password_step2_button_title'] = [
        'tab'    => 'content',
        'group'  => 'ForgetPassword_labels',
        'label'  => esc_html__('Forget password Step 2 Button text', 'bricks'),
        'type'   => 'text',
        'default'=> 'ارسال کد OTP'
    ];


    $this->controls['Forget_password_step3_title'] = [
        'tab'    => 'content',
        'group'  => 'ForgetPassword_labels',
        'label'  => esc_html__('Forget password Step 3 Title', 'bricks'),
        'type'   => 'text',
        'default'=> 'عنوان مرحله اول فراموشی رمز عبور'
    ];

    $this->controls['Forget_password_step3_subTitle'] = [
        'tab'    => 'content',
        'group'  => 'ForgetPassword_labels',
        'label'  => esc_html__('Forget password Step 3 subTitle', 'bricks'),
        'type'   => 'text',
        'default'=> 'زیر عنوان مرحله اول فراموشی رمز عبور'
    ];

    $this->controls['Forget_password_step3_label'] = [
        'tab'    => 'content',
        'group'  => 'ForgetPassword_labels',
        'label'  => esc_html__('Forget password Step 3 Input Label', 'bricks'),
        'type'   => 'text',
        'default'=> 'شماره موبایل خود را وارد کنید'
    ];

    $this->controls['Forget_password_step3_placeholder'] = [
        'tab'    => 'content',
        'group'  => 'ForgetPassword_labels',
        'label'  => esc_html__('Forget password Step 3 Input Placeholder', 'bricks'),
        'type'   => 'text',
        'default'=> ''
    ];

    $this->controls['Forget_password_step3_button_title'] = [
        'tab'    => 'content',
        'group'  => 'ForgetPassword_labels',
        'label'  => esc_html__('Forget password Step 3 Button text', 'bricks'),
        'type'   => 'text',
        'default'=> 'ارسال کد OTP'
    ];

    /*-------------------------
     * STYLE: INPUTS
     *-------------------------*/
    $this->controls['inputBackground'] = [
        'tab'   => 'style',
        'group' => 'style_inputs',
        'label' => esc_html__('Background', 'bricks'),
        'type'  => 'color',
        'css'   => [
            ['property' => 'background-color', 'selector' => '.login-input'],
        ],
    ];

    $this->controls['inputText'] = [
        'tab'   => 'style',
        'group' => 'style_inputs',
        'label' => esc_html__('Text Color', 'bricks'),
        'type'  => 'color',
        'css'   => [
            ['property' => 'color', 'selector' => '.login-input'],
        ],
    ];

    $this->controls['inputBorder'] = [
        'tab'   => 'style',
        'group' => 'style_inputs',
        'label' => esc_html__('Border', 'bricks'),
        'type'  => 'border',
        'css'   => [
            ['property' => 'border', 'selector' => '.login-input'],
        ],
    ];

    $this->controls['inputTypography'] = [
        'tab'   => 'style',
        'group' => 'style_inputs',
        'label' => esc_html__('Typography', 'bricks'),
        'type'  => 'typography',
        'css'   => [
            ['property' => 'font', 'selector' => '.login-input'],
        ],
    ];

    $this->controls['inputPadding'] = [
        'tab'   => 'style',
        'group' => 'style_inputs',
        'label' => esc_html__('Padding', 'bricks'),
        'type'  => 'dimensions',
        'css'   => [
            ['property' => 'padding', 'selector' => '.login-input'],
        ],
    ];

    $this->controls['inputMargin'] = [
        'tab'   => 'style',
        'group' => 'style_inputs',
        'label' => esc_html__('Margin', 'bricks'),
        'type'  => 'dimensions',
        'css'   => [
            ['property' => 'margin', 'selector' => '.login-input'],
        ], 
    ];

    /*-------------------------
     * STYLE: BUTTONS
     *-------------------------*/
    // $this->controls['buttonBackground'] = [
    //     'tab'   => 'style',
    //     'group' => 'style_buttons',
    //     'label' => esc_html__('Background', 'bricks'),
    //     'type'  => 'color',
    //     'css'   => [
    //         ['property' => 'background-color', 'selector' => '.login-btn'],
    //     ],
    // ];

    // $this->controls['buttonText'] = [
    //     'tab'   => 'style',
    //     'group' => 'style_buttons',
    //     'label' => esc_html__('Text Color', 'bricks'),
    //     'type'  => 'color',
    //     'css'   => [
    //         ['property' => 'color', 'selector' => '.login-btn'],
    //     ],
    // ];

    // $this->controls['buttonBorder'] = [
    //     'tab'   => 'style',
    //     'group' => 'style_buttons',
    //     'label' => esc_html__('Border', 'bricks'),
    //     'type'  => 'border',
    //     'css'   => [
    //         ['property' => 'border', 'selector' => '.login-btn'],
    //     ],
    // ];

    // $this->controls['buttonTypography'] = [
    //     'tab'   => 'style',
    //     'group' => 'style_buttons',
    //     'label' => esc_html__('Typography', 'bricks'),
    //     'type'  => 'typography',
    //     'css'   => [
    //         ['property' => 'font', 'selector' => '.login-btn'],
    //     ],
    // ];

    // $this->controls['buttonPadding'] = [
    //     'tab'   => 'style',
    //     'group' => 'style_buttons',
    //     'label' => esc_html__('Padding', 'bricks'),
    //     'type'  => 'dimensions',
    //     'css'   => [
    //         ['property' => 'padding', 'selector' => '.login-btn'],
    //     ],
    // ];

    // $this->controls['buttonHoverBackground'] = [
    //     'tab'   => 'style',
    //     'group' => 'style_buttons',
    //     'label' => esc_html__('Background (Hover)', 'bricks'),
    //     'type'  => 'color',
    //     'css'   => [
    //         ['property' => 'background-color', 'selector' => '.login-btn:hover'],
    //     ],
    // ];

    // $this->controls['buttonHoverText'] = [
    //     'tab'   => 'style',
    //     'group' => 'style_buttons',
    //     'label' => esc_html__('Text (Hover)', 'bricks'),
    //     'type'  => 'color',
    //     'css'   => [
    //         ['property' => 'color', 'selector' => '.login-btn:hover'],
    //     ],
    // ];

    // $this->controls['buttonHoverBorder'] = [
    //     'tab'   => 'style',
    //     'group' => 'style_buttons',
    //     'label' => esc_html__('Border Color (Hover)', 'bricks'),
    //     'type'  => 'color',
    //     'css'   => [
    //         ['property' => 'border-color', 'selector' => '.login-btn:hover'],
    //     ],
    // ];

    /*-------------------------
     * STYLE: LABELS
     *-------------------------*/
    $this->controls['labelColor'] = [
        'tab'   => 'style',
        'group' => 'style_labels',
        'label' => esc_html__('Text Color', 'bricks'),
        'type'  => 'color',
        'css'   => [
            ['property' => 'color', 'selector' => '.login-label'],
        ],
    ];

    $this->controls['labelTypography'] = [
        'tab'   => 'style',
        'group' => 'style_labels',
        'label' => esc_html__('Typography', 'bricks'),
        'type'  => 'typography',
        'css'   => [
            ['property' => 'font', 'selector' => '.login-label'],
        ],
    ];

    $this->controls['labelSpacing'] = [
        'tab'   => 'style',
        'group' => 'style_labels',
        'label' => esc_html__('Margin', 'bricks'),
        'type'  => 'dimensions',
        'css'   => [
            ['property' => 'margin', 'selector' => '.login-label'],
        ],
    ];

    /*-------------------------
     * STYLE: RESEND LINK
     *-------------------------*/
    $this->controls['resendColor'] = [
        'tab'   => 'style',
        'group' => 'style_resend',
        'label' => esc_html__('Background Color', 'bricks'),
        'type'  => 'color',
        'css'   => [
            ['property' => 'background-color', 'selector' => '.resend-otp'],
        ],
    ];

    $this->controls['resendTypography'] = [
        'tab'   => 'style',
        'group' => 'style_resend',
        'label' => esc_html__('Resend Typography', 'bricks'),
        'type'  => 'typography',
        'css'   => [
            ['property' => 'font', 'selector' => '.resend-otp'],
        ],
    ];

    $this->controls['resendPadding'] = [
        'tab'   => 'style',
        'group' => 'style_resend',
        'label' => esc_html__('Resend Padding', 'bricks'),
        'type'  => 'dimensions',
        'css'   => [
            ['property' => 'padding', 'selector' => '.resend-otp'],
        ],
    ];

    $this->controls['resendMargin'] = [
        'tab'   => 'style',
        'group' => 'style_resend',
        'label' => esc_html__('Resend Margin', 'bricks'),
        'type'  => 'dimensions',
        'css'   => [
            ['property' => 'margin', 'selector' => '.resend-otp'],
        ],
    ];

    //login with password

    $this->controls['loginWithPassword_typography'] = [
        'tab'   => 'style',
        'group' => 'style_loginWithPassword',
        'label' => esc_html__('Typography', 'bricks'),
        'type'  => 'typography',
        'css'   => [
            ['property' => 'font', 'selector' => '.login-with-password'],
        ],
    ];

    $this->controls['loginWithPassword_background'] = [
        'tab'   => 'style',
        'group' => 'style_loginWithPassword',
        'label' => esc_html__('Background Color', 'bricks'),
        'type'  => 'color',
        'css'   => [
            ['property' => 'background-color', 'selector' => '.login-with-password'],
        ],
    ];

    $this->controls['loginWithPassword_padding'] = [
        'tab'   => 'style',
        'group' => 'style_loginWithPassword',
        'label' => esc_html__('Padding', 'bricks'),
        'type'  => 'dimensions',
        'css'   => [
            ['property' => 'padding', 'selector' => '.login-with-password'],
        ],
    ];

    $this->controls['loginWithPassword_margin'] = [
        'tab'   => 'style',
        'group' => 'style_loginWithPassword',
        'label' => esc_html__('Margin', 'bricks'),
        'type'  => 'dimensions',
        'css'   => [
            ['property' => 'margin', 'selector' => '.login-with-password'],
        ],
    ];


    /*-------------------------
     * STYLE: MESSAGE TEXT
     *-------------------------*/
    $this->controls['messageColor'] = [
        'tab'   => 'style',
        'group' => 'style_message',
        'label' => esc_html__('Text Color', 'bricks'),
        'type'  => 'color',
        'css'   => [
            ['property' => 'color', 'selector' => '.login-message'],
        ],
    ];

    $this->controls['messageBackground'] = [  // Renamed to avoid duplicate
        'tab'   => 'style',
        'group' => 'style_message',
        'label' => esc_html__('Background Color', 'bricks'),
        'type'  => 'color',
        'css'   => [
            ['property' => 'background-color', 'selector' => '.login-message'],
        ],
    ];

    $this->controls['messageTypography'] = [
        'tab'   => 'style',
        'group' => 'style_message',
        'label' => esc_html__('Typography', 'bricks'),
        'type'  => 'typography',
        'css'   => [
            ['property' => 'font', 'selector' => '.login-message'],
        ],
    ];

    $this->controls['messageMargin'] = [  // Renamed for clarity
        'tab'   => 'style',
        'group' => 'style_message',
        'label' => esc_html__('Margin', 'bricks'),
        'type'  => 'dimensions',
        'css'   => [
            ['property' => 'margin', 'selector' => '.login-message'],
        ],
    ];

    $this->controls['messagePadding'] = [  // Renamed to avoid duplicate
        'tab'   => 'style',
        'group' => 'style_message',
        'label' => esc_html__('Padding', 'bricks'),
        'type'  => 'dimensions',
        'css'   => [
            ['property' => 'padding', 'selector' => '.login-message'],
        ],
    ];

    /*-------------------------
     * STYLE: TITLE & SUBTITLE
     *-------------------------*/
    $this->controls['titleTypography'] = [
        'tab'   => 'style',
        'group' => 'style_title',
        'label' => esc_html__('Title Typography', 'bricks'),
        'type'  => 'typography',
        'css'   => [
            ['property' => 'font', 'selector' => '.login-title'],
        ],
    ];

    $this->controls['titlePadding'] = [
        'tab'   => 'style',
        'group' => 'style_title',
        'label' => esc_html__('Padding', 'bricks'),
        'type'  => 'dimensions',
        'css'   => [
            ['property' => 'padding', 'selector' => '.login-title'],
        ],
    ];

    $this->controls['titleMargin'] = [
        'tab'   => 'style',
        'group' => 'style_title',
        'label' => esc_html__('Margin', 'bricks'),
        'type'  => 'dimensions',
        'css'   => [
            ['property' => 'margin', 'selector' => '.login-title'],
        ],
    ];


    $this->controls['subTitleTypography'] = [
        'tab'   => 'style',
        'group' => 'style_subtitle',
        'label' => esc_html__('Subtitle Typography', 'bricks'),
        'type'  => 'typography',
        'css'   => [
            ['property' => 'font', 'selector' => '.login-subtitle'],
        ],
    ];

    $this->controls['subTitlePadding'] = [
        'tab'   => 'style',
        'group' => 'style_subtitle',
        'label' => esc_html__('Padding', 'bricks'),
        'type'  => 'dimensions',
        'css'   => [
            ['property' => 'padding', 'selector' => '.login-subtitle'],
        ],
    ];

    $this->controls['subTitleMargin'] = [
        'tab'   => 'style',
        'group' => 'style_subtitle',
        'label' => esc_html__('Margin', 'bricks'),
        'type'  => 'dimensions',
        'css'   => [
            ['property' => 'margin', 'selector' => '.login-subtitle'],
        ],
    ];




    $this->controls['subtitleColor'] = [
        'tab'   => 'style',
        'group' => 'typography',
        'label' => esc_html__('Subtitle Color', 'bricks'),
        'type'  => 'color',
        'css'   => [
            ['property' => 'color', 'selector' => '.login-subtitle'],
        ],
    ];

    $this->controls['headerBackground'] = [
        'tab'   => 'style',
        'group' => 'background',
        'label' => esc_html__('Header Background', 'bricks'),
        'type'  => 'color',
        'css'   => [
            ['property' => 'background-color', 'selector' => '.login-header'],
        ],
    ];

    // Generate step-specific controls dynamically to reduce code repetition
    $this->generate_step_controls();

}

/*---------------------------------------------
 * GENERATE STEP-SPECIFIC CONTROLS (DRY Approach)
 *---------------------------------------------*/
private function generate_step_controls() {
    // Button configurations for each step (matched group names)
    $button_steps = [
        'login' => [
            'title'    => 'Login Button',
            'group'    => 'style_login_button',
            'selector' => '.login-btn-send',
            'controls' => ['background', 'text', 'border', 'padding', 'margin', 'width', 'hover_bg', 'hover_text']
        ],
        'password' => [
            'title'    => 'Password Login Button',
            'group'    => 'style_password_button',
            'selector' => '.login-btn-password',
            'controls' => ['background', 'text', 'border', 'padding', 'width']
        ],
        'otp' => [
            'title'    => 'OTP Verify Button',
            'group'    => 'style_otp_button',
            'selector' => '.login-btn-verify',
            'controls' => ['background', 'text', 'padding', 'width','margin']
        ],
        'setpassword' => [
            'title'    => 'Set Password Button',
            'group'    => 'style_setpassword_button',
            'selector' => '.login-btn-register',
            'controls' => ['background', 'text', 'padding']
        ],
        'forgot' => [
            'title'    => 'Forgot Password Button',
            'group'    => 'style_forgot_button',
            'selector' => '.login-btn-forgot',
            'controls' => ['typography', 'text', 'padding', 'margin', 'background']
        ],
        'loginWithOTP' => [
            'title'    => 'Login With OTP Button',
            'group'    => 'style_login_with_otp_button',
            'selector' => '.login-btn-otp',
            'controls' => ['typography', 'text', 'padding', 'margin', 'background']
        ],
        'Buttons' => [
            'title'    => 'Buttons',
            'group'    => 'style_buttons',
            'selector' => '.login-btn',
            'controls' => ['typography', 'text', 'padding', 'margin', 'background','border','height','hover_bg','hover_text']
        ],
    ];

    // Input configurations for each step (matched group names)
    $input_steps = [
        'login' => [
            'title'    => 'Login Input',
            'group'    => 'style_login_input',
            'selector' => '.login-input-login',
            'controls' => ['background', 'border', 'padding', 'width', 'margin']
        ],
        'password' => [
            'title'    => 'Password Input',
            'group'    => 'style_password_input',
            'selector' => '.login-input-password',
            'controls' => ['background', 'border', 'padding', 'width', 'margin']
        ],
        'otp' => [
            'title'    => 'OTP Input',
            'group'    => 'style_otp_input',
            'selector' => '.login-input-otp',
            'controls' => ['background', 'border', 'padding', 'width', 'margin']
        ],
        'setpassword' => [
            'title'    => 'Set Password Input',
            'group'    => 'style_setpassword_input',
            'selector' => '.login-input-setpassword',
            'controls' => ['background', 'border', 'padding', 'width', 'margin']
        ],
        'forgot' => [
            'title'    => 'Forgot Password Input',
            'group'    => 'style_forgot_input',
            'selector' => '.login-input-forgot',
            'controls' => ['background', 'border', 'padding', 'width', 'margin']
        ]
    ];

    // Label configurations for each step
    $label_steps = [
        'login' => [
            'title'    => 'Login Label',
            'group'    => 'style_login_label',
            'selector' => '.login-label-login',
            'controls' => ['color', 'typography', 'margin']
        ],
        'password' => [
            'title'    => 'Password Label',
            'group'    => 'style_password_label',
            'selector' => '.login-label-password',
            'controls' => ['color', 'margin']
        ],
        'otp' => [
            'title'    => 'OTP Label',
            'group'    => 'style_otp_label',
            'selector' => '.login-label-otp',
            'controls' => ['typography', 'margin','padding','border','width']
        ],
        'setpassword' => [
            'title'    => 'Set Password Label',
            'group'    => 'style_setpassword_label',
            'selector' => '.login-label-setpassword',
            'controls' => ['color']
        ],
        'forgot' => [
            'title'    => 'Forgot Password Label',
            'group'    => 'style_forgot_label',
            'selector' => '.login-label-forgot',
            'controls' => ['color']
        ],
        'timer' => [
            'title'    => 'Reset Timer',
            'group'    => 'style_timer_label',
            'selector' => '.resend-timer',
            'controls' => ['typography', 'margin','padding','border','width']
        ],

        'loginPasswordError' => [
            'title'    => 'Login Password Error',
            'group'    => 'style_login_password_error',
            'selector' => '.login-password-error',
            'controls' => ['typography', 'margin','padding','border','width','background']
        ]

    ];

    $addon_input = [
        'password-icon' => [
            'title'    => 'Password Icon',
            'group'    => 'style_password_icon',
            'selector' => '.password-icon',
            'controls' => ['typography', 'margin','padding','border','width','height','background']
        ],
        'country-code' => [
            'title'    => 'Country Code',
            'group'    => 'style_country_code',
            'selector' => '.country-code',
            'controls' => ['typography', 'margin','padding','border','width','height','background']
        ],
    ];

    $message_steps = [
        'success' => [
            'title'    => 'Success Message Text',
            'group'    => 'style_success_message_text',
            'selector' => '.login-message.msg-success',
            'controls' => ['color', 'background', 'typography', 'margin', 'padding', 'border', 'positionOffsets', 'position']
        ],
        'error' => [
            'title'    => 'Error Message Text',
            'group'    => 'style_error_message_text',
            'selector' => '.login-message.msg-danger',
            'controls' => ['color', 'background', 'typography', 'margin', 'padding', 'border' , 'positionOffsets', 'position']
        ],
        'notice' => [
            'title'    => 'Notice Message Text',
            'group'    => 'style_notice_message_text',
            'selector' => '.login-message.msg-notice',
            'controls' => ['color', 'background', 'typography', 'margin', 'padding', 'border', 'positionOffsets', 'position']
        ],
    ];

    // Generate controls for buttons
    foreach ($button_steps as $step => $config) {
        $this->add_step_element_controls('button', $step, $config);
    }

    // Generate controls for inputs
    foreach ($input_steps as $step => $config) {
        $this->add_step_element_controls('input', $step, $config);
    }

    // Generate controls for labels
    foreach ($label_steps as $step => $config) {
        $this->add_step_element_controls('label', $step, $config);
    }

    // Generate controls for addon inputs
    foreach ($addon_input as $step => $config) {
        $this->add_step_element_controls('addon input', $step, $config);
    }

    foreach ($message_steps as $step => $config) {
        $this->add_step_element_controls('message', $step, $config);
    }
}

/*---------------------------------------------
 * ADD INDIVIDUAL STEP ELEMENT CONTROLS
 *---------------------------------------------*/
private function add_step_element_controls($element_type, $step, $config) {
    $control_key_prefix = ($element_type === 'button') ? 'btn' : $element_type;

    // Define control properties
    $control_definitions = [
        'background' => [
            'label'    => esc_html__('Background', 'bricks'),
            'type'     => 'color',
            'property' => 'background-color'
        ],
        'text' => [
            'label'    => esc_html__('Text Color', 'bricks'),
            'type'     => 'color',
            'property' => 'color'
        ],
        'color' => [
            'label'    => esc_html__('Text Color', 'bricks'),
            'type'     => 'color',
            'property' => 'color'
        ],
        'border' => [
            'label'    => esc_html__('Border', 'bricks'),
            'type'     => 'border',
            'property' => 'border'
        ],
        'padding' => [
            'label'    => esc_html__('Padding', 'bricks'),
            'type'     => 'dimensions',
            'property' => 'padding'
        ],
        'margin' => [
            'label'    => esc_html__('Margin', 'bricks'),
            'type'     => 'dimensions',
            'property' => 'margin'
        ],
        'width' => [
            'label'    => esc_html__('Width', 'bricks'),
            'type'     => 'number',
            'units'    => true,
            'property' => 'width'
        ],
        'height' => [
            'label'    => esc_html__('Height', 'bricks'),
            'type'     => 'number',
            'units'    => true,
            'property' => 'height'
        ],
        'typography' => [
            'label'    => esc_html__('Typography', 'bricks'),
            'type'     => 'typography',
            'property' => 'font'
        ],
        'hover_bg' => [
            'label'           => esc_html__('Background (Hover)', 'bricks'),
            'type'            => 'color',
            'property'        => 'background-color',
            'selector_suffix' => ':hover'
        ],
        'hover_text' => [
            'label'           => esc_html__('Text Color (Hover)', 'bricks'),
            'type'            => 'color',
            'property'        => 'color',
            'selector_suffix' => ':hover'
        ],
        'position' => [
            'label'    => esc_html__('Position', 'bricks'),
            'type'     => 'select',
            'options'  => [
                'static'   => esc_html__('Static', 'bricks'),
                'relative' => esc_html__('Relative', 'bricks'),
                'absolute' => esc_html__('Absolute', 'bricks'),
                'fixed'    => esc_html__('Fixed', 'bricks'),
                'sticky'   => esc_html__('Sticky', 'bricks')
            ],
            'property' => 'position',
        ],
        'positionOffsets' => [
            'label'    => esc_html__('Position Offsets', 'bricks'),
            'type'     => 'dimensions',
            'css'      => [
                [
                    'property' => 'top',
                ],
                [
                    'property' => 'right',
                ],
                [
                    'property' => 'bottom',
                ],
                [
                    'property' => 'left',
                ],
            ],
        ]
    ];

    // Generate a control for each specified property
    foreach ($config['controls'] as $control_name) {
        if (!isset($control_definitions[$control_name])) {
            continue;
        }

        $def = $control_definitions[$control_name];
        $control_id = "{$step}_{$control_key_prefix}_{$control_name}";
        $selector = $config['selector'];

        // Append hover suffix if present
        if (!empty($def['selector_suffix'])) {
            $selector .= $def['selector_suffix'];
        }

        $this->controls[$control_id] = [
            'tab'   => 'style',
            'group' => $config['group'],
            'label' => $def['label'],
            'type'  => $def['type'],
            'css'   => [
                ['property' => $def['property'], 'selector' => $selector]
            ]
        ];
    }
}

    /*---------------------------------------------
     * ENQUEUE SCRIPTS
     *--------------------------------------------*/
    public function enqueue_scripts() {
        wp_enqueue_script('login-otp-script');
    }

    /*---------------------------------------------
     * RENDER
     *--------------------------------------------*/
public function render() {
        // Main wrapper classes
        $this->set_attribute('_root', 'class', ['login-otp-wrapper', 'auth-box']);
        $icon_url = plugins_url( 'assets/img/AlertIcon.svg', __FILE__ );
        // Base element classes
        $this->set_attribute('header', 'class', 'auth-header login-header');
        $this->set_attribute('title', 'class', 'auth-title login-title');
        $this->set_attribute('subtitle', 'class', 'auth-subtitle login-subtitle');
        $this->set_attribute('step', 'class', 'auth-step login-step');
        $this->set_attribute('message', 'class', 'auth-message login-message');
        $this->set_attribute('resend_wrapper', 'class', 'auth-resend login-resend');
        // Step-specific login elements
        $this->set_attribute('login_label', 'class', 'auth-step-label login-label login-label-login');
        $this->set_attribute('login_input', 'class', 'auth-step-input login-input login-input-login');
        $this->set_attribute('login_btn_send', 'class', 'auth-btn login-btn login-btn-send');
        $this->set_attribute('login_btn_switch', 'class', 'auth-btn login-btn-switch login-with-password');
        // Step-specific password elements
        $this->set_attribute('password_label', 'class', 'auth-step-label login-label login-label-password');
        $this->set_attribute('password_input', 'class', 'auth-step-input login-input login-input-password');
        $this->set_attribute('password_btn_login', 'class', 'auth-btn login-btn login-btn-password');
        $this->set_attribute('password_btn_forgot', 'class', 'auth-btn login-btn-forgot');
        $this->set_attribute('password_btn_otp', 'class', 'auth-btn login-btn-otp');
        $this->set_attribute('login_password_error', 'class', 'auth-btn login-password-error');
        // icon and country code
        $this->set_attribute('password_icon', 'class', 'password-icon toggle-password');
        $this->set_attribute('country_code', 'class', 'country-code');
        // Step-specific OTP elements
        $this->set_attribute('otp_input', 'class', 'auth-step-input login-input login-input-otp');
        $this->set_attribute('edit_number', 'class', 'auth-step-label login-label login-label-otp');
        $this->set_attribute('otp_btn_verify', 'class', 'auth-btn login-btn login-btn-verify');
        $this->set_attribute('otp_btn_resend', 'class', 'resend-otp login-btn-resend');
        $this->set_attribute('resend_timer','class','resend-timer');
        // Step-specific set password elements
        $this->set_attribute('setpassword_label', 'class', 'auth-step-label login-label login-label-setpassword');
        $this->set_attribute('setpassword_input', 'class', 'auth-step-input login-input login-input-setpassword');
        $this->set_attribute('setpassword_btn', 'class', 'auth-btn login-btn login-btn-register');
        $this->set_attribute('password_wrapper', 'class', 'login-password-wrapper');
        // Step-specific forgot password elements
        $this->set_attribute('forgot_label', 'class', 'auth-step-label login-label login-label-forgot');
        $this->set_attribute('forgot_input', 'class', 'auth-step-input login-input login-input-forgot');
        $this->set_attribute('forgot_btn', 'class', 'auth-btn login-btn login-btn-forgot');

        // message attributes
        // $this->set_attribute('success_message', 'data-role', '.login-message.msg-success');
        // $this->set_attribute('danger_message', 'data-role', '.login-message.msg-danger');
        // $this->set_attribute('notice_message', 'data-role', '.login-message.msg-notice');

        // متغیرهای محتوا
        $phone_label = esc_html($this->settings['phone_label'] ?? 'شماره تلفن همراه');
        $login_title = esc_html($this->settings['login_title'] ?? '');
        $login_subtitle = esc_html($this->settings['login_subtitle'] ?? '');
        $login_label = esc_html($this->settings['login_label'] ?? '');
        $login_placeholder = esc_html($this->settings['login_placeholder'] ?? '');
        $login_button_title = esc_html($this->settings['login_button_title'] ?? '');
        $login_switch_password = esc_html($this->settings['login_switch_password'] ?? '');
        $login_password_title = esc_html($this->settings['login_password_title'] ?? '');
        $login_password_Subtitle = esc_html($this->settings['login_password_Subtitle'] ?? '');
        $login_password_username_label = esc_html($this->settings['login_password_username_label'] ?? '');
        $login_password_username_placeholder = esc_html($this->settings['login_password_username_placeholder'] ?? '');
        $login_password_password_label = esc_html($this->settings['login_password_password_label'] ?? '');
        $login_password_password_placeholder = esc_html($this->settings['login_password_password_placeholder'] ?? '');
        $login_password_button_title = esc_html($this->settings['login_password_button_title'] ?? '');
        $login_password_forgetPassword_title = esc_html($this->settings['login_password_forgetPassword_title'] ?? '');
        $login_password_loginOTP_title = esc_html($this->settings['login_password_loginOTP_title'] ?? '');
        $login_OTP_title = esc_html($this->settings['login_OTP_title'] ?? '');
        $login_OTP_EditNumber = esc_html($this->settings['login_OTP_EditNumber'] ?? '');
        $login_OTP_placeholder = esc_html($this->settings['login_OTP_placeholder'] ?? '');
        $login_OTP_button_title = esc_html($this->settings['login_OTP_button_title'] ?? '');
        $login_OTP_resendCode = esc_html($this->settings['login_OTP_resendCode'] ?? '');
        $set_password_title = esc_html($this->settings['set_password_title'] ?? '');
        $set_password_subTitle = esc_html($this->settings['set_password_subTitle'] ?? '');
        $set_password_label = esc_html($this->settings['set_password_label'] ?? '');
        $set_password_placeholder = esc_html($this->settings['set_password_placeholder'] ?? '');
        $set_password_button_title = esc_html($this->settings['set_password_button_title'] ?? '');
        $Forget_password_step1_title = esc_html($this->settings['Forget_password_step1_title'] ?? '');
        $Forget_password_step1_subTitle = esc_html($this->settings['Forget_password_step1_subTitle'] ?? '');
        $Forget_password_step1_label = esc_html($this->settings['Forget_password_step1_label'] ?? '');
        $Forget_password_step1_placeholder = esc_html($this->settings['Forget_password_step1_placeholder'] ?? '');
        $Forget_password_step1_button_title = esc_html($this->settings['Forget_password_step1_button_title'] ?? '');
        $Forget_password_step2_title = esc_html($this->settings['Forget_password_step2_title'] ?? '');
        $Forget_password_step2_subTitle = esc_html($this->settings['Forget_password_step2_subTitle'] ?? '');
        $Forget_password_step2_label = esc_html($this->settings['Forget_password_step2_label'] ?? '');
        $Forget_password_step2_placeholder = esc_html($this->settings['Forget_password_step2_placeholder'] ?? '');
        $Forget_password_step2_button_title = esc_html($this->settings['Forget_password_step2_button_title'] ?? '');
        $Forget_password_step3_title = esc_html($this->settings['Forget_password_step3_title'] ?? '');
        $Forget_password_step3_subTitle = esc_html($this->settings['Forget_password_step3_subTitle'] ?? '');
        $Forget_password_step3_label = esc_html($this->settings['Forget_password_step3_label'] ?? '');
        $Forget_password_step3_placeholder = esc_html($this->settings['Forget_password_step3_placeholder'] ?? '');
        $Forget_password_step3_button_title = esc_html($this->settings['Forget_password_step3_button_title'] ?? '');
        $country_code = esc_html($this->settings['country_code_text'] ?? '');
        
        $otp_length = intval(get_option('otp_login_otp_length', 4));

        
        // Countdown and OTP length
        $this->set_attribute('_root', 'data-countdown', esc_attr($this->settings['countdown'] ?? intval(get_option('otp_login_countdown', 120))));
        $this->set_attribute('_root', 'data-otp-length', esc_attr($this->settings['otp_length'] ?? $otp_length));
        echo "<div {$this->render_attributes('_root')}>";
        // Step 1: Phone (OTP Login)
        echo "<div {$this->render_attributes('step')} data-role='step' data-step='1' style='display:none;'  >
                    <div {$this->render_attributes('header')}>
                            <h2 {$this->render_attributes('title')} data-role='form-title'>{$login_title}</h2>
                            <p {$this->render_attributes('subtitle')} data-role='form-subtitle'>{$login_subtitle}</p>
                    </div>
                    <div>
                        <label {$this->render_attributes('login_label')}>{$login_label}</label>
                        <div {$this->render_attributes('login_input')}>
                            <input type='tel' data-role='phone' placeholder='{$login_placeholder}' />
                            <span {$this->render_attributes('country_code')}>{$country_code}</span>
                        </div>
                        <div {$this->render_attributes('message')} data-role='message'></div>
                    </div>
                    <div class='button-container'>
                        <button {$this->render_attributes('login_btn_send')} data-role='send'>{$login_button_title}</button>
                        <button {$this->render_attributes('login_btn_switch')} data-role='switch-to-password'>{$login_switch_password}</button>
                    </div>
                </div>";
        // Step 1b: Phone + Password (Password Login)
        echo "<div {$this->render_attributes('step')} data-role='step' data-step='password' >
                <div {$this->render_attributes('header')}>
                    <h2 {$this->render_attributes('title')} data-role='form-title'>{$login_password_title}</h2>
                    <p {$this->render_attributes('subtitle')} data-role='form-subtitle'>{$login_password_Subtitle}</p>
                </div>
                <div>
                    <div>
                        <label {$this->render_attributes('password_label')}>{$login_password_username_label}</label>
                        <div {$this->render_attributes('password_input')}>
                            <input type='tel' data-role='phone' placeholder='{$login_password_username_placeholder}' />
                            <span {$this->render_attributes('country_code')}>{$country_code}</span>
                        </div>
                    </div>
                    <div>
                        <label {$this->render_attributes('password_label')}>{$login_password_password_label}</label>
                        <div {$this->render_attributes('password_input')} data-role='password-wrapper'>
                            <input
                                type='password'
                                data-role='password-field'
                                placeholder='{$login_password_password_placeholder}'
                            >
                                <span {$this->render_attributes('password_icon')}
                                    data-role='toggle-password'
                                    aria-label='نمایش یا مخفی کردن رمز عبور'>
                                    <!-- eye open -->
                                    <svg class='icon-eye-open' xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none'>
                                        <g clip-path='url(#clip0_11536_341)'>
                                            <path fill-rule='evenodd' clip-rule='evenodd' d='M12.0001 2.65503C18.1921 2.65503 21.7201 6.89303 23.2711 9.41903C23.7505 10.1945 24.0045 11.0883 24.0045 12C24.0045 12.9118 23.7505 13.8055 23.2711 14.581C21.7201 17.107 18.1921 21.345 12.0001 21.345C5.80805 21.345 2.28005 17.107 0.729051 14.581C0.249579 13.8055 -0.00439453 12.9118 -0.00439453 12C-0.00439453 11.0883 0.249579 10.1945 0.729051 9.41903C2.28005 6.89303 5.80805 2.65503 12.0001 2.65503ZM12.0001 19.345C17.2191 19.345 20.2341 15.7 21.5661 13.534C21.8509 13.0731 22.0018 12.5419 22.0018 12C22.0018 11.4582 21.8509 10.927 21.5661 10.466C20.2341 8.29603 17.2191 4.65503 12.0001 4.65503C6.78105 4.65503 3.76605 8.30003 2.43405 10.466C2.14919 10.927 1.9983 11.4582 1.9983 12C1.9983 12.5419 2.14919 13.0731 2.43405 13.534C3.76605 15.7 6.78105 19.345 12.0001 19.345ZM9.2222 7.84268C10.0444 7.29327 11.0111 7.00003 12.0001 7.00003C13.3256 7.00162 14.5965 7.52891 15.5338 8.46625C16.4712 9.40359 16.9985 10.6744 17.0001 12C17.0001 12.9889 16.7068 13.9556 16.1574 14.7779C15.608 15.6001 14.8271 16.241 13.9135 16.6194C12.9998 16.9979 11.9945 17.0969 11.0246 16.904C10.0547 16.711 9.16378 16.2348 8.46452 15.5356C7.76526 14.8363 7.28905 13.9454 7.09613 12.9755C6.9032 12.0056 7.00222 11.0002 7.38065 10.0866C7.75909 9.17298 8.39995 8.39209 9.2222 7.84268ZM10.3333 14.4944C10.8267 14.8241 11.4067 15 12.0001 15C12.7957 15 13.5588 14.684 14.1214 14.1214C14.684 13.5587 15.0001 12.7957 15.0001 12C15.0001 11.4067 14.8241 10.8267 14.4945 10.3333C14.1648 9.83997 13.6963 9.45545 13.1481 9.22839C12.5999 9.00133 11.9967 8.94192 11.4148 9.05767C10.8328 9.17343 10.2983 9.45915 9.87873 9.87871C9.45917 10.2983 9.17345 10.8328 9.0577 11.4148C8.94194 11.9967 9.00135 12.5999 9.22841 13.1481C9.45547 13.6963 9.83999 14.1648 10.3333 14.4944Z' fill='#AAAAAA'/>
                                        </g>
                                        <defs>
                                            <clipPath id='clip0_11536_341'>
                                            <rect width='24' height='24' fill='white'/>
                                            </clipPath>
                                        </defs>
                                    </svg>
                                    <!-- eye closed -->
                                    <svg class='icon-eye-closed' xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none'>
                                    <g clip-path='url(#clip0_11536_17)'>
                                        <path d='M23.2711 9.41897C22.3683 7.9407 21.2296 6.62023 19.9001 5.50997L22.7001 2.70997C22.8822 2.52137 22.983 2.26877 22.9807 2.00657C22.9784 1.74437 22.8733 1.49356 22.6879 1.30815C22.5025 1.12274 22.2516 1.01758 21.9895 1.0153C21.7273 1.01302 21.4747 1.11381 21.2861 1.29597L18.2411 4.34497C16.3534 3.22379 14.1955 2.63943 12.0001 2.65497C5.80905 2.65497 2.28105 6.89297 0.729051 9.41897C0.249579 10.1945 -0.00439453 11.0882 -0.00439453 12C-0.00439453 12.9117 0.249579 13.8055 0.729051 14.581C1.63181 16.0592 2.77054 17.3797 4.10005 18.49L1.30005 21.29C1.20454 21.3822 1.12836 21.4926 1.07595 21.6146C1.02354 21.7366 0.995955 21.8678 0.994801 22.0006C0.993647 22.1334 1.01895 22.265 1.06923 22.3879C1.11951 22.5108 1.19376 22.6225 1.28766 22.7164C1.38155 22.8103 1.4932 22.8845 1.6161 22.9348C1.73899 22.9851 1.87067 23.0104 2.00345 23.0092C2.13623 23.0081 2.26745 22.9805 2.38946 22.9281C2.51146 22.8757 2.6218 22.7995 2.71405 22.704L5.76605 19.652C7.65132 20.773 9.80672 21.3583 12.0001 21.345C18.1911 21.345 21.7191 17.107 23.2711 14.581C23.7505 13.8055 24.0045 12.9117 24.0045 12C24.0045 11.0882 23.7505 10.1945 23.2711 9.41897ZM2.43305 13.534C2.14819 13.073 1.9973 12.5418 1.9973 12C1.9973 11.4581 2.14819 10.9269 2.43305 10.466C3.76705 8.29997 6.78205 4.65497 12.0001 4.65497C13.6603 4.64567 15.2973 5.04581 16.7661 5.81997L14.7531 7.83297C13.793 7.19557 12.642 6.90997 11.4953 7.02462C10.3486 7.13927 9.27692 7.64711 8.46205 8.46197C7.64719 9.27684 7.13935 10.3485 7.0247 11.4952C6.91005 12.6419 7.19565 13.7929 7.83305 14.753L5.52305 17.063C4.29815 16.0727 3.25289 14.8789 2.43305 13.534ZM15.0001 12C15.0001 12.7956 14.684 13.5587 14.1214 14.1213C13.5588 14.6839 12.7957 15 12.0001 15C11.5546 14.9982 11.1152 14.8957 10.7151 14.7L14.7001 10.715C14.8958 11.1152 14.9983 11.5545 15.0001 12ZM9.00005 12C9.00005 11.2043 9.31612 10.4413 9.87873 9.87865C10.4413 9.31604 11.2044 8.99997 12.0001 8.99997C12.4455 9.0017 12.8849 9.10426 13.2851 9.29997L9.30005 13.285C9.10434 12.8848 9.00178 12.4455 9.00005 12ZM21.5671 13.534C20.2331 15.7 17.2181 19.345 12.0001 19.345C10.3398 19.3543 8.70282 18.9541 7.23405 18.18L9.24705 16.167C10.2071 16.8044 11.3581 17.09 12.5048 16.9753C13.6515 16.8607 14.7232 16.3528 15.538 15.538C16.3529 14.7231 16.8608 13.6514 16.9754 12.5047C17.0901 11.3581 16.8044 10.207 16.1671 9.24697L18.4771 6.93697C19.7019 7.92725 20.7472 9.12102 21.5671 10.466C21.8519 10.9269 22.0028 11.4581 22.0028 12C22.0028 12.5418 21.8519 13.073 21.5671 13.534Z' fill='#AAAAAA'/>
                                    </g>
                                    <defs>
                                        <clipPath id='clip0_11536_17'>
                                        <rect width='24' height='24' fill='white'/>
                                        </clipPath>
                                    </defs>
                                    </svg>
                                </span>
                        </div>
                    </div>
                    <div {$this->render_attributes('message')} data-role='message'></div>
                    <button {$this->render_attributes('password_btn_forgot')} data-role='forgot'>{$login_password_forgetPassword_title}</button>
                </div>
                <div class='button-container'>
                    <button {$this->render_attributes('password_btn_login')} data-role='login-password'>{$login_password_button_title}</button>
                    <button {$this->render_attributes('password_btn_otp')} data-role='switch-to-otp'>{$login_password_loginOTP_title}</button>
                </div>
              </div>";
        // Step 2: OTP
        echo "<div {$this->render_attributes('step')} data-role='step' data-step='2' style='display:none;'  >
                    <div {$this->render_attributes('header')}>
                            <h2 {$this->render_attributes('title')} data-role='form-title'>{$login_OTP_title}</h2>
                            <div {$this->render_attributes('subtitle')}>
                                <p data-role='user-message'></p>
                                <p data-role='display-phone'></p>
                                <p data-role='display-email'></p>
                            </div>
                    </div>
                    <div>
                        <p {$this->render_attributes('edit_number')} data-role='edit-number' cursor-pointer = true>{$login_OTP_EditNumber}</p>
                        <div {$this->render_attributes('login_input')}>
                            <input type='tel' data-role='otp' placeholder='{$Forget_password_step2_placeholder}' />
                        </div>
                        <p {$this->render_attributes('resend_timer')} data-role='timer'></p>
                        <div {$this->render_attributes('resend_wrapper')}>
                            <button {$this->render_attributes('otp_btn_resend')} data-role='resend' disabled>{$login_OTP_resendCode}</button>
                        </div>
                        <div {$this->render_attributes('message')} data-role='message'></div>
                    </div>
                    <div>
                        <button {$this->render_attributes('otp_btn_verify')} data-role='verify' disabled>{$login_OTP_button_title}</button>
                    </div>
               </div>";
        // Step 3: Set Password
        echo "
        <div {$this->render_attributes('step')} data-role='step' data-step='3' style='display:none;' >
                <div {$this->render_attributes('header')}>
                    <h2 {$this->render_attributes('title')} data-role='form-title'>{$set_password_title}</h2>
                    <p {$this->render_attributes('subtitle')} data-role='form-subtitle'>{$set_password_subTitle}</p>
                </div>
                <div>
                    <label {$this->render_attributes('setpassword_label')}>{$set_password_label}</label>
                    <div {$this->render_attributes('password_input')} data-role='password-wrapper'>
                                <input
                                    type='password'
                                    data-role='password-field'
                                    placeholder='{$login_password_password_placeholder}'
                                >
                                    <span {$this->render_attributes('password_icon')}
                                        data-role='toggle-password'
                                        aria-label='نمایش یا مخفی کردن رمز عبور'>
                                        <!-- eye open -->
                                        <svg class='icon-eye-open' xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none'>
                                            <g clip-path='url(#clip0_11536_341)'>
                                                <path fill-rule='evenodd' clip-rule='evenodd' d='M12.0001 2.65503C18.1921 2.65503 21.7201 6.89303 23.2711 9.41903C23.7505 10.1945 24.0045 11.0883 24.0045 12C24.0045 12.9118 23.7505 13.8055 23.2711 14.581C21.7201 17.107 18.1921 21.345 12.0001 21.345C5.80805 21.345 2.28005 17.107 0.729051 14.581C0.249579 13.8055 -0.00439453 12.9118 -0.00439453 12C-0.00439453 11.0883 0.249579 10.1945 0.729051 9.41903C2.28005 6.89303 5.80805 2.65503 12.0001 2.65503ZM12.0001 19.345C17.2191 19.345 20.2341 15.7 21.5661 13.534C21.8509 13.0731 22.0018 12.5419 22.0018 12C22.0018 11.4582 21.8509 10.927 21.5661 10.466C20.2341 8.29603 17.2191 4.65503 12.0001 4.65503C6.78105 4.65503 3.76605 8.30003 2.43405 10.466C2.14919 10.927 1.9983 11.4582 1.9983 12C1.9983 12.5419 2.14919 13.0731 2.43405 13.534C3.76605 15.7 6.78105 19.345 12.0001 19.345ZM9.2222 7.84268C10.0444 7.29327 11.0111 7.00003 12.0001 7.00003C13.3256 7.00162 14.5965 7.52891 15.5338 8.46625C16.4712 9.40359 16.9985 10.6744 17.0001 12C17.0001 12.9889 16.7068 13.9556 16.1574 14.7779C15.608 15.6001 14.8271 16.241 13.9135 16.6194C12.9998 16.9979 11.9945 17.0969 11.0246 16.904C10.0547 16.711 9.16378 16.2348 8.46452 15.5356C7.76526 14.8363 7.28905 13.9454 7.09613 12.9755C6.9032 12.0056 7.00222 11.0002 7.38065 10.0866C7.75909 9.17298 8.39995 8.39209 9.2222 7.84268ZM10.3333 14.4944C10.8267 14.8241 11.4067 15 12.0001 15C12.7957 15 13.5588 14.684 14.1214 14.1214C14.684 13.5587 15.0001 12.7957 15.0001 12C15.0001 11.4067 14.8241 10.8267 14.4945 10.3333C14.1648 9.83997 13.6963 9.45545 13.1481 9.22839C12.5999 9.00133 11.9967 8.94192 11.4148 9.05767C10.8328 9.17343 10.2983 9.45915 9.87873 9.87871C9.45917 10.2983 9.17345 10.8328 9.0577 11.4148C8.94194 11.9967 9.00135 12.5999 9.22841 13.1481C9.45547 13.6963 9.83999 14.1648 10.3333 14.4944Z' fill='#AAAAAA'/>
                                            </g>
                                            <defs>
                                                <clipPath id='clip0_11536_341'>
                                                <rect width='24' height='24' fill='white'/>
                                                </clipPath>
                                            </defs>
                                        </svg>
                                        <!-- eye closed -->
                                        <svg class='icon-eye-closed' xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none'>
                                        <g clip-path='url(#clip0_11536_17)'>
                                            <path d='M23.2711 9.41897C22.3683 7.9407 21.2296 6.62023 19.9001 5.50997L22.7001 2.70997C22.8822 2.52137 22.983 2.26877 22.9807 2.00657C22.9784 1.74437 22.8733 1.49356 22.6879 1.30815C22.5025 1.12274 22.2516 1.01758 21.9895 1.0153C21.7273 1.01302 21.4747 1.11381 21.2861 1.29597L18.2411 4.34497C16.3534 3.22379 14.1955 2.63943 12.0001 2.65497C5.80905 2.65497 2.28105 6.89297 0.729051 9.41897C0.249579 10.1945 -0.00439453 11.0882 -0.00439453 12C-0.00439453 12.9117 0.249579 13.8055 0.729051 14.581C1.63181 16.0592 2.77054 17.3797 4.10005 18.49L1.30005 21.29C1.20454 21.3822 1.12836 21.4926 1.07595 21.6146C1.02354 21.7366 0.995955 21.8678 0.994801 22.0006C0.993647 22.1334 1.01895 22.265 1.06923 22.3879C1.11951 22.5108 1.19376 22.6225 1.28766 22.7164C1.38155 22.8103 1.4932 22.8845 1.6161 22.9348C1.73899 22.9851 1.87067 23.0104 2.00345 23.0092C2.13623 23.0081 2.26745 22.9805 2.38946 22.9281C2.51146 22.8757 2.6218 22.7995 2.71405 22.704L5.76605 19.652C7.65132 20.773 9.80672 21.3583 12.0001 21.345C18.1911 21.345 21.7191 17.107 23.2711 14.581C23.7505 13.8055 24.0045 12.9117 24.0045 12C24.0045 11.0882 23.7505 10.1945 23.2711 9.41897ZM2.43305 13.534C2.14819 13.073 1.9973 12.5418 1.9973 12C1.9973 11.4581 2.14819 10.9269 2.43305 10.466C3.76705 8.29997 6.78205 4.65497 12.0001 4.65497C13.6603 4.64567 15.2973 5.04581 16.7661 5.81997L14.7531 7.83297C13.793 7.19557 12.642 6.90997 11.4953 7.02462C10.3486 7.13927 9.27692 7.64711 8.46205 8.46197C7.64719 9.27684 7.13935 10.3485 7.0247 11.4952C6.91005 12.6419 7.19565 13.7929 7.83305 14.753L5.52305 17.063C4.29815 16.0727 3.25289 14.8789 2.43305 13.534ZM15.0001 12C15.0001 12.7956 14.684 13.5587 14.1214 14.1213C13.5588 14.6839 12.7957 15 12.0001 15C11.5546 14.9982 11.1152 14.8957 10.7151 14.7L14.7001 10.715C14.8958 11.1152 14.9983 11.5545 15.0001 12ZM9.00005 12C9.00005 11.2043 9.31612 10.4413 9.87873 9.87865C10.4413 9.31604 11.2044 8.99997 12.0001 8.99997C12.4455 9.0017 12.8849 9.10426 13.2851 9.29997L9.30005 13.285C9.10434 12.8848 9.00178 12.4455 9.00005 12ZM21.5671 13.534C20.2331 15.7 17.2181 19.345 12.0001 19.345C10.3398 19.3543 8.70282 18.9541 7.23405 18.18L9.24705 16.167C10.2071 16.8044 11.3581 17.09 12.5048 16.9753C13.6515 16.8607 14.7232 16.3528 15.538 15.538C16.3529 14.7231 16.8608 13.6514 16.9754 12.5047C17.0901 11.3581 16.8044 10.207 16.1671 9.24697L18.4771 6.93697C19.7019 7.92725 20.7472 9.12102 21.5671 10.466C21.8519 10.9269 22.0028 11.4581 22.0028 12C22.0028 12.5418 21.8519 13.073 21.5671 13.534Z' fill='#AAAAAA'/>
                                        </g>
                                        <defs>
                                            <clipPath id='clip0_11536_17'>
                                            <rect width='24' height='24' fill='white'/>
                                            </clipPath>
                                        </defs>
                                        </svg>
                                    </span>
                    </div>
                    <div {$this->render_attributes('message')} data-role='message'></div>
                </div>
                    <button {$this->render_attributes('setpassword_btn')} data-role='register'>{$set_password_button_title}</button>
        </div>";
        // Forgot Password Steps
        echo "<div {$this->render_attributes('step')} data-step='forgot-1' style='display:none;'>
                <div {$this->render_attributes('header')}>
                        <h2 {$this->render_attributes('title')} data-role='form-title'>{$Forget_password_step1_title}</h2>
                        <p {$this->render_attributes('subtitle')} data-role='form-subtitle'>{$Forget_password_step1_subTitle}</p>
                </div>
                <div>
                    <label {$this->render_attributes('forgot_label')}>{$Forget_password_step1_label}</label>
                    <div {$this->render_attributes('login_input')}>
                        <input type='tel' data-role='phone' placeholder='{$Forget_password_step1_placeholder}' />
                        <span {$this->render_attributes('country_code')}>{$country_code}</span>
                    </div>
                    <div {$this->render_attributes('message')}  data-role='message'></div>
                </div>
                <button {$this->render_attributes('forgot_btn')} data-role='send-forgot'>{$Forget_password_step1_button_title}</button>
              </div>";
        echo "<div {$this->render_attributes('step')}  data-step='forgot-2' style='display:none;' >
                <div {$this->render_attributes('header')}>
                        <h2 {$this->render_attributes('title')} data-role='form-title'>{$Forget_password_step2_title}</h2>
                        <p {$this->render_attributes('subtitle')} data-role='form-subtitle'>{$Forget_password_step2_subTitle}</p>
                </div>
                <div>
                    <p {$this->render_attributes('edit_number')} data-role='edit-number' cursor-pointer = true>{$login_OTP_EditNumber}</p>
                    <div {$this->render_attributes('login_input')}>
                        <input type='tel' data-role='otp' placeholder='{$Forget_password_step2_placeholder}' />
                    </div>
                    <p {$this->render_attributes('resend_timer')} data-role='timer'></p>
                    <div {$this->render_attributes('resend_wrapper')}>
                        <button {$this->render_attributes('otp_btn_resend')} data-role='resend' disabled>{$login_OTP_resendCode}</button>
                    </div>
                    <div {$this->render_attributes('message')} data-role='message'></div>
                </div>
                <button {$this->render_attributes('forgot_btn')} data-role='verify-forgot' disabled>{$Forget_password_step2_button_title}</button>
              </div>";
        echo "<div {$this->render_attributes('step')} data-step='forgot-3' style='display:none;'>
                <div {$this->render_attributes('header')}>
                        <h2 {$this->render_attributes('title')} data-role='form-title'>{$Forget_password_step3_title}</h2>
                        <p {$this->render_attributes('subtitle')} data-role='form-subtitle'>{$Forget_password_step3_subTitle}</p>
                </div>
                <div>
                    <label {$this->render_attributes('forgot_label')}>{$Forget_password_step3_label}</label>
                    <div {$this->render_attributes('password_input')} data-role='password-wrapper'>
                        <input
                            type='password'
                            data-role='new-password'
                            placeholder='{$login_password_password_placeholder}'
                        >
                            <span {$this->render_attributes('password_icon')}
                                data-role='toggle-password'
                                aria-label='نمایش یا مخفی کردن رمز عبور'>
                                <!-- eye open -->
                                 <svg class='icon-eye-open' xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none'>
                                    <g clip-path='url(#clip0_11536_341)'>
                                        <path fill-rule='evenodd' clip-rule='evenodd' d='M12.0001 2.65503C18.1921 2.65503 21.7201 6.89303 23.2711 9.41903C23.7505 10.1945 24.0045 11.0883 24.0045 12C24.0045 12.9118 23.7505 13.8055 23.2711 14.581C21.7201 17.107 18.1921 21.345 12.0001 21.345C5.80805 21.345 2.28005 17.107 0.729051 14.581C0.249579 13.8055 -0.00439453 12.9118 -0.00439453 12C-0.00439453 11.0883 0.249579 10.1945 0.729051 9.41903C2.28005 6.89303 5.80805 2.65503 12.0001 2.65503ZM12.0001 19.345C17.2191 19.345 20.2341 15.7 21.5661 13.534C21.8509 13.0731 22.0018 12.5419 22.0018 12C22.0018 11.4582 21.8509 10.927 21.5661 10.466C20.2341 8.29603 17.2191 4.65503 12.0001 4.65503C6.78105 4.65503 3.76605 8.30003 2.43405 10.466C2.14919 10.927 1.9983 11.4582 1.9983 12C1.9983 12.5419 2.14919 13.0731 2.43405 13.534C3.76605 15.7 6.78105 19.345 12.0001 19.345ZM9.2222 7.84268C10.0444 7.29327 11.0111 7.00003 12.0001 7.00003C13.3256 7.00162 14.5965 7.52891 15.5338 8.46625C16.4712 9.40359 16.9985 10.6744 17.0001 12C17.0001 12.9889 16.7068 13.9556 16.1574 14.7779C15.608 15.6001 14.8271 16.241 13.9135 16.6194C12.9998 16.9979 11.9945 17.0969 11.0246 16.904C10.0547 16.711 9.16378 16.2348 8.46452 15.5356C7.76526 14.8363 7.28905 13.9454 7.09613 12.9755C6.9032 12.0056 7.00222 11.0002 7.38065 10.0866C7.75909 9.17298 8.39995 8.39209 9.2222 7.84268ZM10.3333 14.4944C10.8267 14.8241 11.4067 15 12.0001 15C12.7957 15 13.5588 14.684 14.1214 14.1214C14.684 13.5587 15.0001 12.7957 15.0001 12C15.0001 11.4067 14.8241 10.8267 14.4945 10.3333C14.1648 9.83997 13.6963 9.45545 13.1481 9.22839C12.5999 9.00133 11.9967 8.94192 11.4148 9.05767C10.8328 9.17343 10.2983 9.45915 9.87873 9.87871C9.45917 10.2983 9.17345 10.8328 9.0577 11.4148C8.94194 11.9967 9.00135 12.5999 9.22841 13.1481C9.45547 13.6963 9.83999 14.1648 10.3333 14.4944Z' fill='#AAAAAA'/>
                                    </g>
                                    <defs>
                                        <clipPath id='clip0_11536_341'>
                                        <rect width='24' height='24' fill='white'/>
                                        </clipPath>
                                    </defs>
                                </svg>
                                <!-- eye closed -->
                                <svg class='icon-eye-closed' xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none'>
                                <g clip-path='url(#clip0_11536_17)'>
                                    <path d='M23.2711 9.41897C22.3683 7.9407 21.2296 6.62023 19.9001 5.50997L22.7001 2.70997C22.8822 2.52137 22.983 2.26877 22.9807 2.00657C22.9784 1.74437 22.8733 1.49356 22.6879 1.30815C22.5025 1.12274 22.2516 1.01758 21.9895 1.0153C21.7273 1.01302 21.4747 1.11381 21.2861 1.29597L18.2411 4.34497C16.3534 3.22379 14.1955 2.63943 12.0001 2.65497C5.80905 2.65497 2.28105 6.89297 0.729051 9.41897C0.249579 10.1945 -0.00439453 11.0882 -0.00439453 12C-0.00439453 12.9117 0.249579 13.8055 0.729051 14.581C1.63181 16.0592 2.77054 17.3797 4.10005 18.49L1.30005 21.29C1.20454 21.3822 1.12836 21.4926 1.07595 21.6146C1.02354 21.7366 0.995955 21.8678 0.994801 22.0006C0.993647 22.1334 1.01895 22.265 1.06923 22.3879C1.11951 22.5108 1.19376 22.6225 1.28766 22.7164C1.38155 22.8103 1.4932 22.8845 1.6161 22.9348C1.73899 22.9851 1.87067 23.0104 2.00345 23.0092C2.13623 23.0081 2.26745 22.9805 2.38946 22.9281C2.51146 22.8757 2.6218 22.7995 2.71405 22.704L5.76605 19.652C7.65132 20.773 9.80672 21.3583 12.0001 21.345C18.1911 21.345 21.7191 17.107 23.2711 14.581C23.7505 13.8055 24.0045 12.9117 24.0045 12C24.0045 11.0882 23.7505 10.1945 23.2711 9.41897ZM2.43305 13.534C2.14819 13.073 1.9973 12.5418 1.9973 12C1.9973 11.4581 2.14819 10.9269 2.43305 10.466C3.76705 8.29997 6.78205 4.65497 12.0001 4.65497C13.6603 4.64567 15.2973 5.04581 16.7661 5.81997L14.7531 7.83297C13.793 7.19557 12.642 6.90997 11.4953 7.02462C10.3486 7.13927 9.27692 7.64711 8.46205 8.46197C7.64719 9.27684 7.13935 10.3485 7.0247 11.4952C6.91005 12.6419 7.19565 13.7929 7.83305 14.753L5.52305 17.063C4.29815 16.0727 3.25289 14.8789 2.43305 13.534ZM15.0001 12C15.0001 12.7956 14.684 13.5587 14.1214 14.1213C13.5588 14.6839 12.7957 15 12.0001 15C11.5546 14.9982 11.1152 14.8957 10.7151 14.7L14.7001 10.715C14.8958 11.1152 14.9983 11.5545 15.0001 12ZM9.00005 12C9.00005 11.2043 9.31612 10.4413 9.87873 9.87865C10.4413 9.31604 11.2044 8.99997 12.0001 8.99997C12.4455 9.0017 12.8849 9.10426 13.2851 9.29997L9.30005 13.285C9.10434 12.8848 9.00178 12.4455 9.00005 12ZM21.5671 13.534C20.2331 15.7 17.2181 19.345 12.0001 19.345C10.3398 19.3543 8.70282 18.9541 7.23405 18.18L9.24705 16.167C10.2071 16.8044 11.3581 17.09 12.5048 16.9753C13.6515 16.8607 14.7232 16.3528 15.538 15.538C16.3529 14.7231 16.8608 13.6514 16.9754 12.5047C17.0901 11.3581 16.8044 10.207 16.1671 9.24697L18.4771 6.93697C19.7019 7.92725 20.7472 9.12102 21.5671 10.466C21.8519 10.9269 22.0028 11.4581 22.0028 12C22.0028 12.5418 21.8519 13.073 21.5671 13.534Z' fill='#AAAAAA'/>
                                </g>
                                <defs>
                                    <clipPath id='clip0_11536_17'>
                                    <rect width='24' height='24' fill='white'/>
                                    </clipPath>
                                </defs>
                                </svg>
                            </span>
                    </div>
                    <div {$this->render_attributes('message')} data-role='message'></div>
                </div>
                <button {$this->render_attributes('forgot_btn')} data-role='reset-password'>{$Forget_password_step3_button_title}</button>
              </div>";
        echo "</div>"; // end _root
    }
}