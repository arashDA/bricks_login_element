## OTP Login for Bricks

A WordPress plugin that provides OTP-based login functionality integrated with Bricks Builder. It supports login via SMS OTP (using Melipayamak API), email OTP for existing users, password login, and password reset flows. Includes a custom Bricks element for easy integration into your site.

## Features

- **OTP Login via SMS**: Send verification codes to users' phones using Melipayamak SMS service.
- **Email OTP Fallback**: Automatically sends OTP via email to existing users with valid email addresses.
- **Password Login**: Alternative login with phone and password.
- **User Registration**: Automatically registers new users with auto-generated emails if not found.
- **Password Reset**: Forgot password flow with OTP verification and new password setup.
- **Bricks Builder Integration**: Custom element for drag-and-drop login forms in Bricks.
- **Customizable Settings**: Admin panel for configuring SMS credentials, OTP length, countdown timer, and redirect URL.
- **Secure and Asynchronous**: Uses WordPress nonces, AJAX handlers, and async email sending.
- **Responsive Design**: Frontend assets (CSS/JS) for a mobile-friendly login experience.
- **Countdown Timer**: For OTP resend with visual feedback.
- **Error Handling**: User-friendly messages for invalid inputs, expired OTPs, etc.

## Requirements

- WordPress 5.0+
- Bricks Builder 1.0+
- PHP 7.4+
- Melipayamak account for SMS (username, password, and template ID required)
- Database access (creates a custom table for OTP storage)

## Installation

1. Download the plugin ZIP file.
2. In your WordPress admin dashboard, go to **Plugins > Add New**.
3. Click **Upload Plugin** and select the ZIP file.
4. Activate the plugin.
5. Upon activation, the plugin creates a database table for OTP storage.

Alternatively, install via FTP:
- Upload the plugin folder to `/wp-content/plugins/`.
- Activate the plugin in the WordPress admin.

## Configuration

After activation, configure the plugin settings:

1. Go to **WP Admin > OTP Login** (under the menu).
2. Enter your Melipayamak credentials:
   - Username
   - Password
   - Template ID (for OTP messages)
3. Set general options:
   - Redirect After Login: URL to redirect users after successful login (default: homepage).
   - Resend Countdown: Time in seconds before allowing OTP resend (default: 120).
   - OTP Length: Number of digits in the OTP code (default: 6).
4. Save changes.

**Note**: SMS sending requires valid Melipayamak credentials. Test thoroughly to ensure delivery.

## Usage

### Adding the Login Form in Bricks

1. Edit a page or template in Bricks Builder.
2. Search for the "Login OTP" element in the elements panel (under the "webcoq" category).
3. Drag it onto your canvas.
4. Customize the element's controls in the Bricks sidebar:
   - **Content Tab**: Labels, placeholders, titles, subtitles for each step (Login, Password, OTP, Set Password, Forgot Password).
   - **Style Tab**: Customize inputs, buttons, labels, messages, icons, etc., with typography, colors, backgrounds, and more.
   - Icons: Upload custom back and site logos.
5. Save and preview the page.

### Login Flows

- **OTP Login**:
  - Enter phone number → Send OTP → Verify OTP → Login/Register.
- **Password Login**:
  - Switch to password mode → Enter phone and password → Login.
- **Forgot Password**:
  - Enter phone → Send OTP (only if user exists) → Verify OTP → Set new password.

The form handles auto-focus, input validation, Persian/English digit conversion, and loading states.

### Frontend Assets

- CSS: `assets/css/login.css` (styles for messages, buttons, loaders).
- JS: `assets/js/login.js` (handles AJAX, timers, input events).
- Icons: SVG assets for error/notice/success and password toggle.

Assets are enqueued on the frontend and in Bricks editor previews.

## Database

- Creates a table: `{prefix}_login_otp` for storing phone, OTP code, expiration, and verification status.
- Table is created on activation via `dbDelta`.

## Hooks and Actions

- AJAX Endpoints: `login_send_otp`, `login_verify_otp`, `login_register_user`, etc.
- Async Email: Uses `wp_schedule_single_event` for non-blocking email OTPs.
- Custom Hooks: `login_send_email_otp_async`.

## Troubleshooting

- **SMS Not Sending**: Check Melipayamak credentials in settings. Ensure the phone number is in the correct format (10 digits, no leading zeros or country codes).
- **Email Not Sending**: Verify `wp_mail` works on your site. Emails are only sent to existing users with non-generated emails.
- **OTP Table Issues**: Deactivate/reactivate the plugin to recreate the table.
- **Bricks Element Not Showing**: Ensure Bricks is active and the element file (`bricks/elements/login.php`) is loaded.
- **JavaScript Errors**: Check console for nonce issues or AJAX URL mismatches.
- **Phone Normalization**: Handles Persian digits and common prefixes (0098, 098, etc.).

For logs: Check WordPress error logs for SMS/email failures.

## Development

- **Files**:
  - `otp-login.php`: Main plugin file (activation, hooks, AJAX).
  - `email.php`: Email OTP sending logic.
  - `bricks/elements/login.php`: Bricks element class (controls, rendering).
  - `assets/js/login.js`: Frontend JavaScript (AJAX, timers, inputs).
  - `assets/css/login.css`: Styles for messages and loaders.
- **Customization**: Extend via WordPress hooks or modify the Bricks element controls.

## Changelog

### 1.0 - Initial Release (Release Date: [Insert Release Date])

- Initial release of the plugin.
- Added core features: OTP login via SMS and email, password login, user registration, and password reset.
- Integrated with Bricks Builder via a custom "Login OTP" element.
- Admin settings panel for SMS credentials, OTP configuration, and redirect options.
- Frontend assets for responsive design, countdown timers, and error messaging.
- Database table for OTP storage and secure AJAX handlers.

## License

This plugin is licensed under the GPLv2 or later. See [LICENSE](LICENSE) for details.

## Support

- Author: Arash Dadjoo
- For issues: Open a GitHub issue or contact via email (arashdadjoo3@gmail.com).
- Contributions: Pull requests welcome!

If you find this plugin useful, consider starring the repo or leaving a review!