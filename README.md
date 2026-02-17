# Otp Login for Bricks

![Version](https://img.shields.io/badge/version-1.1.0-blue.svg)
![License](https://img.shields.io/badge/license-GPL--2.0+-green.svg)
![WordPress](https://img.shields.io/badge/WordPress-5.0+-blue.svg)

A professional WordPress plugin for secure, multi-provider OTP authentication fully integrated with Bricks Builder. Transition from traditional passwords to a seamless mobile-first login experience with real-time styling controls.

## ✨ Features

### Request Monitoring
- **Real-time SMS Tracking**: Monitor OTP delivery through integrated provider logs.  
- **Verification Status**: Track successful vs. failed login attempts in the database.  
- **AJAX State Handling**: Real-time UI feedback for loading, success, and error states.  

### Request Management
- **Multi-Provider SMS**: Built-in support for Melipayamak, MsgWay, SMS.ir, and Kavenegar.  
- **Email Fallback**: Automatically switch to email delivery if SMS credit is low or service is unavailable.  
- **Phone Normalization**: Automatic conversion of Persian/Arabic digits to English for API compatibility.  

### Professional UI
- **Bricks Native**: A dedicated custom element that lives directly inside the Bricks Builder.  
- **Live Preview**: Assets (CSS/JS) load within the editor so you see exactly what you’re building.  
- **Dynamic Icons**: Customizable SVG support for Site Logos, Back Buttons, and Message Icons.  
- **Mobile Optimized**: Auto-focusing inputs and zoom-prevention logic for a better mobile UX.  

### Settings & Configuration
- **Tabbed Admin UI**: A modern, JavaScript-powered settings page for easy configuration.  
- **Global OTP Controls**: Set custom OTP lengths and resend countdown timers.  
- **Template Management**: Configure SMS Template IDs and Email headers globally.  
- **Custom Login Redirect**: Define where users are redirected after successful authentication.  
- **Custom Logout Redirect**: Control where users are redirected after logout. Defaults to homepage if not configured.  

### Database & Logs
- **Dedicated Table**: Uses a custom `wp_login_otp` table for high-performance code verification.  
- **Auto-Cleanup**: Efficiently manages expired codes to keep your database lean.  


## 🚀 Installation

### Requirements
- **WordPress**: 5.8+
- **Bricks Builder**: 1.5+
- **PHP**: 7.4 or higher
- **SMS Account**: Credentials for Melipayamak, MsgWay, SMS.ir , Kavenegar and IPpanel

1. Upload the `otp-login` folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the **Plugins** menu in WordPress.
3. Go to **Otp Login** in the admin sidebar to configure your API keys.

## 📖 Usage

### Dashboard
- The main settings page allows you to choose your active SMS provider and enter your API credentials.
- Toggle between providers instantly without losing settings for the others.

### Managing Requests
- All authentication happens via the `wp-admin/admin-ajax.php` endpoint. 
- Ensure your firewall allows requests to your chosen SMS provider's API endpoints.

### Review Modal
- Use the **Bricks Builder** to drag the "Login OTP" element onto any page.
- Use the "Content" tab to modify labels and the "Style" tab to control colors, spacing, and typography.

### Settings
- **General**: Set redirect URLs for successful logins and the default OTP timeout.
- **Provider-Specific**: Enter Template IDs and API Keys for your specific SMS service.

### Clear Logs
- Expired OTP entries are automatically invalidated, ensuring codes cannot be reused after the timeout period.

## Architecture

### File Structure
- `otp-login.php`: Core logic and AJAX handler registration.
- `includes/sms-senders.php`: The SMS provider registry and API implementations.
- `includes/email.php`: Fallback email delivery logic.
- `bricks/elements/login.php`: The Bricks Builder element definition and controls.
- `assets/js/login.js`: Frontend state machine for the multi-step form.

### Database Tables
- `wp_login_otp`: 
    - `phone`: User identifier.
    - `code`: Hashed/Stored OTP.
    - `expires`: Expiry timestamp.
    - `verified`: Boolean flag for successful checks.

## 🔐 Security
- **Nonces**: All AJAX actions are secured with WordPress nonces.
- **Input Sanitization**: All phone numbers and codes are sanitized before database entry.
- **Secure Redirects**: Post-login redirection uses `wp_safe_redirect`.

## 🎯 Use Cases
- **LMS Sites**: Fast login for students via mobile number.
- **E-commerce**: Reduce cart abandonment with one-click phone verification.
- **Client Portals**: Secure access without requiring users to remember complex passwords.

## 📊 API Reference
The plugin uses a registry pattern in `sms-senders.php`. You can extend it to support new providers by adding a case to the `login_send_sms` function.

## 🐛 Troubleshooting
- **SMS Not Sending**: Verify your Template ID and ensure your account has enough credit.
- **Styling Not Reflecting**: Clear your Bricks Builder cache and regenerate CSS.
- **Timer Issues**: Ensure your server time matches the visitor's local timezone.

## 📝 Changelog

### Version 1.1.0 - Current
- **New**: Added support for MsgWay, SMS.ir , Kavenegar and IPpanel providers.
- **New**: Redesigned Admin Settings page with dynamic provider switching.
- **Improvement**: Added Site Logo and Back Icon controls to Bricks element.
- **Improvement**: Enqueued editor-specific scripts for live styling previews.

### Version 1.0.0
- Initial release with Melipayamak SMS support.
- Core Bricks Element integration.

## 📜 License
This plugin is licensed under the GPL-2.0+ license.

## 🤝 Contributing

Contributions are welcome! Please:
1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## 📧 Support
For bug reports and feature requests, please use the [GitHub Issues](https://github.com/arashDA/bricks_login_element/issues) page.

## 👨‍💻 Author
**Arash Dadjoo**
- Website: https://arashdadjoo.ir/
- GitHub: @arashDA

**Made with ❤️ for WordPress developers**