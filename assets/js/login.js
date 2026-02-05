(function () {
    'use strict';

    function qs(sel, ctx) { 
        return (ctx || document).querySelector(sel); 
    }

    function postData(url, data) {
        return fetch(url, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
            body: new URLSearchParams(data).toString(),
            credentials: 'same-origin'
        }).then(r => r.json());
    }

    // Move helpers outside the click listener
    function hideAllSteps(wrapper) {  // Pass wrapper as param for scope
        const steps = wrapper.querySelectorAll('[data-step]');
        steps.forEach(s => { s.style.display = 'none'; });
    }

    function showStep(wrapper, name) {
        clearButtonLoading(wrapper);
        hideAllSteps(wrapper);  // Always hide everything first
        const s = wrapper.querySelector("[data-step='" + name + "']");
        if (s) {
            s.style.display = 'flex';  // Or 'block' if not using flex
            // Focus first input to activate keyboard without zoom
            requestAnimationFrame(() => {
                const firstInput = s.querySelector('input:not([type="hidden"])');
                if (firstInput) {
                    // Temporarily set font-size to 16px to prevent iOS zoom on focus
                    const originalFontSize = firstInput.style.fontSize || getComputedStyle(firstInput).fontSize;  // Fallback to computed style
                    firstInput.style.fontSize = '16px';
                    firstInput.focus();
                    // Reset font-size after focus
                    firstInput.style.fontSize = originalFontSize;
                }
            });
        }
    }

    function setButtonLoading(btn, loading = true) {
        if (!btn) return;
        btn.classList.toggle('is-loading', loading);
    }
  

    function clearButtonLoading(wrapper) {
        const btns = wrapper.querySelectorAll('button.is-loading');
        btns.forEach(btn => setButtonLoading(btn, false));
    }


    // Regex for changing number
    document.addEventListener('input', function (e) {
        const input = e.target;
        // Only process phone inputs
        if (input.getAttribute('data-role') !== 'phone') return;

        let value = input.value.trim();

        // Convert Persian digits to English
        const persianToEnglish = {
            '۰': '0', '۱': '1', '۲': '2', '۳': '3', '۴': '4',
            '۵': '5', '۶': '6', '۷': '7', '۸': '8', '۹': '9'
        };
        value = value.replace(/[۰-۹]/g, (digit) => persianToEnglish[digit]);

        // Remove +98 if it starts with that
        if (value.startsWith('+98')) {
            value = value.replace(/^\+98/, '');
        }
        // Remove leading 0 if it starts with 09
        else if (value.startsWith('09')) {
            value = value.replace(/^0/, '');
        }

        input.value = value;
    });

    document.addEventListener('click', function (e) {
        const btn = e.target.closest('[data-role]');
        if (!btn) return;

        const wrapper = btn.closest('.login-otp-wrapper');
        if (!wrapper) return;

        const role = btn.getAttribute('data-role'); 
        const phoneInput = qs("[data-role='phone']", wrapper);
        const otpInput = qs("[data-role='otp']", wrapper);

        if (!wrapper._state) {
            // Prefer admin-localized values from loginOtpData, fall back to dataset or defaults
            const defaultCountdown = (typeof loginOtpData !== 'undefined' && loginOtpData.countdown) ? parseInt(loginOtpData.countdown, 10) : 120;
            const defaultOtpLen = (typeof loginOtpData !== 'undefined' && loginOtpData.otpLength) ? parseInt(loginOtpData.otpLength, 10) : 4;

            wrapper._state = {
                timer: null,
                countdown: parseInt(wrapper.dataset.countdown || defaultCountdown, 10),
                otpLength: parseInt(wrapper.dataset.otpLength || defaultOtpLen, 10)
            };
        }

        // Find the message element inside the currently visible step so messages
        // don't bleed across different steps (OTP, password, forgot flows).
        function getActiveMessage() {
            const steps = wrapper.querySelectorAll("[data-step]");
            let visibleStep = null;
            for (let i = 0; i < steps.length; i++) {
                const s = steps[i];
                const cs = window.getComputedStyle(s);
                if (cs && cs.display !== 'none') { visibleStep = s; break; }
            }
            return visibleStep ? qs("[data-role='message']", visibleStep) : qs("[data-role='message']", wrapper);
        }

        function showMessage(text, messageType = 'success') {
            const message = getActiveMessage();
            if (!message || message === 'undefined') return;

            // Icon mapping for different message types
            const icons = {
                danger: `<img src="${loginOtpData.iconDanger}" class="msg-icon" alt="Error">`,
                error: `<img src="${loginOtpData.iconDanger}" class="msg-icon" alt="Error">`,
                notice: `<img src="${loginOtpData.iconNotice}" class="msg-icon" alt="Notice">`,
                warning: `<img src="${loginOtpData.iconNotice}" class="msg-icon" alt="Warning">`,
                success: `<img src="${loginOtpData.iconSuccess}" class="msg-icon" alt="Success">`
            };

            // Prepend icon to message text
            let displayText = (icons[messageType] || '') + text;
            message.innerHTML = displayText;
            
            // Remove all message type classes
            message.classList.remove('msg-danger', 'msg-notice', 'msg-success');
            
            // Add the appropriate message type class
            message.classList.add('msg-' + messageType);
            message.classList.add('login-message');

            message.classList.remove('top', 'bottom');
            message.classList.add(wrapper.dataset.msgPosition);
            message.style.display = 'block';
        }



        function startCountdown(seconds, stepName = '2') {
            clearInterval(wrapper._state.timer);
            const total = seconds || wrapper._state.countdown || ((typeof loginOtpData !== 'undefined' && loginOtpData.countdown) ? parseInt(loginOtpData.countdown, 10) : 120);
            wrapper._state.countdown = total;
            
            // Get step-scoped resend button, timer, and wrapper
            const resendBtn = qs("[data-step='" + stepName + "'] [data-role='resend']", wrapper);
            const timerEl = qs("[data-step='" + stepName + "'] [data-role='timer']", wrapper);
            const resendWrapper = resendBtn ? resendBtn.closest('div') : null;
            
            if (!resendBtn || !timerEl) return;
            
            resendBtn.disabled = true;
            resendBtn.setAttribute('aria-disabled', 'true');

            // Hide resend button, show timer
            if (resendWrapper) resendWrapper.style.display = 'none';
            if (timerEl) timerEl.style.display = 'block';

            function fmt(s) {
                const m = Math.floor(s / 60);
                const sec = s % 60;
                return `(${String(m).padStart(2, '0')}:${String(sec).padStart(2, '0')})`;
            }

            timerEl.textContent = fmt(wrapper._state.countdown);
            wrapper._state.timer = setInterval(() => {
                wrapper._state.countdown--;
                if (wrapper._state.countdown <= 0) {
                    clearInterval(wrapper._state.timer);
                    resendBtn.disabled = false;
                    resendBtn.removeAttribute('aria-disabled');
                    timerEl.textContent = '';

                    // Hide timer, show resend button
                    if (timerEl) timerEl.style.display = 'none';
                    if (resendWrapper) resendWrapper.style.display = 'block';
                } else {
                    timerEl.innerHTML = `<span style="font-weight:bold;">${fmt(wrapper._state.countdown)}</span> مانده تا ارسال مجدد کد تایید`;
                }
            }, 1000);
        }

        // SWITCH TO PASSWORD LOGIN
        if (role === 'switch-to-password') {
            showStep(wrapper, 'password');
            // Copy phone value into password step if empty
            const mainPhone = qs("[data-step='1'] [data-role='phone']", wrapper);
            const pwdPhone = qs("[data-step='password'] [data-role='phone']", wrapper);
            if (mainPhone && pwdPhone && !pwdPhone.value.trim()) pwdPhone.value = mainPhone.value;
            return;
        }

        // SWITCH BACK TO OTP LOGIN
        if (role === 'switch-to-otp') {
            showStep(wrapper, '1');
            if (otpInput) otpInput.value = '';
            return;
        }

        if (role === 'edit-number') {
            showStep(wrapper, '1');
            const phoneField = qs("[data-step='1'] [data-role='phone']", wrapper);
            if (phoneField) phoneField.focus();
            if (otpInput) otpInput.value = '';
            return;
        }

        if (role === 'edit-number-forget') {
            showStep(wrapper, 'forgot-1');
            showMessage()
            const phoneField = qs("[data-step='forgot-1'] [data-role='phone']", wrapper);
            if (phoneField) phoneField.focus();
            if (otpInput) otpInput.value = '';
            return;
        }

        // PASSWORD LOGIN VERIFY
        if (role === 'login-password') {
            // Get current phone and password fields
            const passwordStep = qs("[data-step='password']", wrapper);
            const phoneField = (passwordStep && qs("[data-role='phone']", passwordStep)) || qs("[data-role='phone']", wrapper);
            const phone = phoneField ? phoneField.value.trim() : '';
            const passwordField = qs("[data-role='password-field']", wrapper);
            const password = passwordField ? passwordField.value.trim() : '';


            if (!phone) { showMessage(wrapper.dataset.msgPhoneRequired || 'شماره موبایل خود را وارد کنید', 'danger'); phoneInput.focus(); return; }
            if (!password) { showMessage(wrapper.dataset.msgPasswordRequired || 'رمز عبور خود را وارد کنید', 'danger'); return; }

            showMessage(wrapper.dataset.msgLoggingIn || 'در حال ورود...', 'notice');

            setButtonLoading(btn,true);

            postData(loginOtpData.ajaxUrl, {
                action: 'login_verify_password',
                phone: phone,
                password: password,
                _ajax_nonce: loginOtpData.nonce
            })
            .then(res => {
                
                if (res.success) {
                    showMessage(wrapper.dataset.msgLoggedIn || 'ورود موفقیت امیز بود', 'success');
                    const redirect = res.data?.redirect || loginOtpData.redirect
                    setTimeout(() => window.location.href = redirect, 500);
                } else {
                    showMessage('نام کاربری یا رمز عبور اشتباه است.', 'notice');

                }
            })
            .catch(() => showMessage(wrapper.dataset.msgNetworkError || 'خطایی در ارتباط با سرور رخ داده است.', 'danger'))
            .finally(() => {
                        setButtonLoading(btn, false);
            });
            return;
        }

        // SEND OTP
        if (role === 'send') {
            const phone = phoneInput.value.trim();
            if (!phone) { showMessage(wrapper.dataset.msgPhoneRequired || 'شماره موبایل خود را وارد کنید', 'danger'); phoneInput.focus(); return; }

            showMessage(wrapper.dataset.msgSending || 'در حال ارسال کد تایید...', 'notice');
            setButtonLoading(btn,true);

            postData(loginOtpData.ajaxUrl, {
                action: 'login_send_otp',
                phone: phone,
                _ajax_nonce: loginOtpData.nonce
            })
            .then(res => {
                if (res.success) {
                    showStep(wrapper, '2');
                    showMessage(wrapper.dataset.msgOtpSent || 'کد یکبار مصرف ارسال شد', 'success');

                    postData(loginOtpData.ajaxUrl, {
                        action: 'login_get_user_info',
                        phone: phone,
                        _ajax_nonce: loginOtpData.nonce
                    })
                    .then(userRes => {
                        if (!userRes.success) return;

                        const email = userRes?.data?.email || '';

                        // Display phone number
                        const phoneDisplay = qs("[data-step='2'] [data-role='display-phone']", wrapper);
                        if (phoneDisplay) phoneDisplay.textContent = userRes.data.phone;

                        const emailDisplay = qs("[data-step='2'] [data-role='display-email']", wrapper);
                        const emailRow = qs("[data-step='2'] [data-info='email-row']", wrapper);
                        const msgBox = qs("[data-step='2'] [data-role='user-message']", wrapper);

                        const isRealEmail =
                            /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email) &&
                            !email.endsWith('@OtpPlugin.com');

                        // Normalize response: treat fake email as empty
                        userRes.data.email = isRealEmail ? email : '';

                        if (userRes.data.exists && userRes.data.email && emailDisplay && emailRow) {
                            emailDisplay.textContent = userRes.data.email;
                            emailRow.style.display = 'block';
                            msgBox.textContent = "کد تایید به شماره موبایل و ایمیل زیر ارسال شد";
                        } else {
                            if (emailRow) emailRow.style.display = 'none';
                            msgBox.textContent = "کد تایید به شماره موبایل زیر ارسال شد";
                        }
                    })
                    .catch(() => {})
                    .finally(() => {
                            setButtonLoading(btn, false);
                    });


                    // Focus on OTP input with keyboard activation
                    const newOtp = qs("[data-step='2'] [data-role='otp']", wrapper);
                    if (newOtp) {
                        newOtp.style.fontSize = '16px';
                        newOtp.focus();
                        setTimeout(() => { newOtp.style.fontSize = ''; }, 0);
                    }
                    startCountdown(null, '2');
                } else {
                    showMessage(res.data?.message || wrapper.dataset.msgFailedSend || 'ارسال کد تایید با خطا مواجه شد', 'danger');
                }
            })
            .catch(() => showMessage(wrapper.dataset.msgNetworkError || 'خطایی در ارتباط با سرور رخ داده است.', 'danger'))
            .finally(() => {
                    setButtonLoading(btn, false);
            });
        }

        // RESEND
        if (role === 'resend') {
            // Determine which step we're in (2 or forgot-2)
            const step = btn.closest('[data-step]');
            const stepName = step ? step.getAttribute('data-step') : '2';
            
            // Get phone from the appropriate step
            const phoneField = qs("[data-step='" + stepName.split('-')[0] + "'] [data-role='phone']", wrapper) || phoneInput;
            const phone = phoneField ? phoneField.value.trim() : '';
            
            if (!phone) { showMessage(wrapper.dataset.msgPhoneRequired || 'شماره موبایل خود را وارد کنید', 'danger'); return; }

            showMessage(wrapper.dataset.msgResending || 'در حال ارسال مجدد کد...', 'notice');

            setButtonLoading(btn,true);

            postData(loginOtpData.ajaxUrl, { 
                action: stepName.startsWith('forgot') ? 'login_send_otp_forgot' : 'login_send_otp', 
                phone: phone,
                _ajax_nonce: loginOtpData.nonce 
            })
            .then(res => {
                if (res.success) {
                    showMessage(wrapper.dataset.msgOtpResent || 'کد تایید مجدد ارسال شد', 'success');
                    startCountdown(null, stepName);
                    const newOtp = qs("[data-step='" + stepName + "'] [data-role='otp']", wrapper);
                    if (newOtp) newOtp.focus();
                } else {
                    showMessage(res.data?.message || wrapper.dataset.msgFailedSend || 'ارسال مجدد کد تایید با خطا مواجه شد', 'danger');
                }
            })
            .catch(() => showMessage(wrapper.dataset.msgNetworkError || 'خطایی در ارتباط با سرور رخ داده است.', 'danger'))
            .finally(() => {
                    setButtonLoading(btn, false);
            });
        }

        // VERIFY OTP
        if (role === 'verify') {
            
            
            const phone = phoneInput.value.trim();
            const otp = otpInput.value.trim();


            if (!otp || otp.length < wrapper._state.otpLength) {
                otpInput.focus();
                return;
            }

            setButtonLoading(btn,true);
            postData(loginOtpData.ajaxUrl, { 
                action: 'login_verify_otp', 
                phone: phone, 
                otp: otp, 
                _ajax_nonce: loginOtpData.nonce 
            })
            .then(res => {
                if (res.success) {

                    // Existing user → login
                    if (res.data.status === 'logged_in') {
                        showMessage(wrapper.dataset.msgLoggedIn || 'ورود موفقیت آمیز', 'success');
                        const redirect = res.data?.redirect || loginOtpData.redirect 
                        setTimeout(() => window.location.href = redirect, 500);
                    }

                    

                    // New user → show password step
                    if (res.data.status === 'need_password') {
                        wrapper._state.forgotPhone = phone; // Store phone for next step
                        wrapper._state.loginRedirect = res.data?.redirect; // Store redirect URL
                        showStep(wrapper, '3');
                        showMessage(wrapper.dataset.msgNeedPassword || 'کد تایید شد. لطفاً رمز عبور خود را وارد کنید.', 'success');
                    }

                } else {
                    showMessage(res.data?.message || wrapper.dataset.msgInvalid || 'کد تایید نامعتبر یا منقضی شده است', 'danger');
                    otpInput.focus();
                }
            })
            .catch(() => showMessage(wrapper.dataset.msgNetworkError || 'خطایی در ارتباط با سرور رخ داده است.', 'danger'))
            .finally(() => {
                    setButtonLoading(btn, false);
            });
        }

        // ----------------------------------------------------
        // STEP 5 — REGISTER NEW USER AFTER OTP
        // ----------------------------------------------------
        if (role === 'register') {
            
            // find phone/password fields scoped to the register step, fallback to general inputs
            const phoneFieldReg = qs("[data-step='3'] [data-role='phone']", wrapper) || qs("[data-role='phone']", wrapper) || phoneInput;
            const phone = phoneFieldReg ? (phoneFieldReg.value || '').trim() : '';

            const passwordFieldReg = qs("[data-step='3'] [data-role='password-field']", wrapper) || qs("[data-step='3'] [data-role='new-password']", wrapper);
            const password = passwordFieldReg ? (passwordFieldReg.value || '').trim() : '';


            if (!password) {
                showMessage(wrapper.dataset.msgPasswordRequired || 'لطفاً رمز عبور خود را وارد کنید', 'danger');
                if (passwordFieldReg) passwordFieldReg.focus();
                return;
            }

            if (password.length < 8) {
                showMessage('رمز عبور باید حداقل 8 کاراکتر باشد', 'danger');
                if (passwordFieldReg) passwordFieldReg.focus();
                return;
            }

            showMessage(wrapper.dataset.msgRegistering || 'در حال ثبت نام...', 'notice');
            setButtonLoading(btn,true);
            postData(loginOtpData.ajaxUrl, {
                action: 'login_register_user',
                phone: phone,
                password: password,
                _ajax_nonce: loginOtpData.nonce
            })
            .then(res => {
                setButtonLoading(btn,false);
                if (res.success) {
                    showMessage(wrapper.dataset.msgRegisterSuccess || 'ثبت نام با موفقیت انجام شد', 'success');
                    const redirect = wrapper._state?.loginRedirect || res.data?.redirect || loginOtpData.redirect
                    setTimeout(() => window.location.href = redirect, 500);
                } else {
                    showMessage(res.data?.message || wrapper.dataset.msgRegisterFailed || 'ثبت نام ناموفق بود', 'danger');
                }
            })
            .catch(() => showMessage(wrapper.dataset.msgNetworkError || 'خطایی در ارتباط با سرور رخ داده است.', 'danger'))
            .finally(() => {
                    setButtonLoading(btn, false);
            });
        }

        // ----------------------------------------------------
        // FORGOT PASSWORD FLOW
        // ----------------------------------------------------
        if (role === 'forgot') {
            showStep(wrapper, 'forgot-1');
        }

        // Send OTP for forgot password
        if (role === 'send-forgot') {
            const forgotStep = qs("[data-step='forgot-1']", wrapper);
            const phoneField = (forgotStep && qs("[data-role='phone']", forgotStep)) || qs("[data-role='phone']", wrapper);
            const phone = phoneField ? phoneField.value.trim() : '';

            if (!phone) { showMessage(wrapper.dataset.msgPhoneRequired || 'لطفاً شماره تلفن خود را وارد کنید', 'danger'); if (phoneField) phoneField.focus(); return; }

            // First check whether the phone exists to avoid sending OTP to unknown numbers
            showMessage(wrapper.dataset.msgSending || 'در حال بررسی شماره تلفن...', 'notice');
            setButtonLoading(btn,true);

            postData(loginOtpData.ajaxUrl, {
                action: 'login_check_phone',
                phone: phone,
                _ajax_nonce: loginOtpData.nonce
            })
            .then(checkRes => {
                setButtonLoading(btn,false);
                if (!checkRes.success) {
                    showMessage(checkRes.data?.message || wrapper.dataset.msgFailedSend || 'حسابی با این شماره یافت نشد', 'danger');
                    if (phoneField) phoneField.focus();
                    return;
                }

                // Phone exists — send OTP
                showMessage(wrapper.dataset.msgSending || 'در حال ارسال کد تایید...', 'notice');
                return postData(loginOtpData.ajaxUrl, {
                    action: 'login_send_otp_forgot',
                    phone: phone,
                    _ajax_nonce: loginOtpData.nonce
                })
                .then(res => {
                    if (res.success) {
                        // remember phone for the forgot flow so verify/reset can use it
                        wrapper._state = wrapper._state || {};
                        wrapper._state.forgotPhone = phone;

                        showStep(wrapper, 'forgot-2');
                        showMessage(wrapper.dataset.msgForgotOtpSent || 'کد تایید ارسال شد', 'success');
                        const otpField = qs("[data-step='forgot-2'] [data-role='otp']", wrapper);
                        if (otpField) otpField.focus();
                        startCountdown(null, 'forgot-2');
                    } else {
                        showMessage(res.data?.message || wrapper.dataset.msgFailedSend || 'ارسال کد تایید ناموفق بود', 'danger');
                    }
                });
            })
            .catch(() => showMessage(wrapper.dataset.msgNetworkError || 'خطایی در ارتباط با سرور رخ داده است.', 'danger'))
            .finally(() => {
                    setButtonLoading(btn, false);
            });
        }

        // Verify OTP for forgot password
        if (role === 'verify-forgot') {
            const phone = wrapper._state?.forgotPhone || qs("[data-step='forgot-1'] [data-role='phone']", wrapper)?.value.trim() || phoneInput.value.trim();

            // Prefer OTP inside the forgot-2 step (scoped), fallback to any otp in wrapper
            const otpField = qs("[data-step='forgot-2'] [data-role='otp']", wrapper) || qs("[data-role='otp']", wrapper);
            const otp = otpField ? otpField.value.trim() : '';

            if (!otp || otp.length < (wrapper._state?.otpLength || parseInt(wrapper.dataset.otpLength || ((typeof loginOtpData !== 'undefined' && loginOtpData.otpLength) ? loginOtpData.otpLength : 6), 10))) {
                if (otpField) otpField.focus();
                return;
            }

            showMessage(wrapper.dataset.msgVerifying || 'در حال بررسی کد تایید...', 'notice');
            setButtonLoading(btn,true);
            postData(loginOtpData.ajaxUrl, {
                action: 'login_forgot_verify_otp',
                phone: phone,
                otp: otp,
                _ajax_nonce: loginOtpData.nonce
            })
            .then(res => {
                if (res.success && res.data.status === 'reset_password') {
                    wrapper._state.forgotPhone = phone; // Store phone for password reset
                    showStep(wrapper, 'forgot-3');
                    showMessage(wrapper.dataset.msgForgotVerified || 'کد تایید شد. رمز عبور جدید را وارد کنید.', 'success');
                } else {
                    showMessage(res.data?.message || wrapper.dataset.msgInvalid || 'کد تایید نامعتبر است', 'danger');
                }
            })
            .catch(() => showMessage(wrapper.dataset.msgNetworkError || 'خطایی در ارتباط با سرور رخ داده است.', 'danger'))
            .finally(() => {
                    setButtonLoading(btn, false);
            });
        }

        // Reset password
        if (role === 'reset-password') {
            const phone = wrapper._state?.forgotPhone || qs("[data-step='forgot-1'] [data-role='phone']", wrapper)?.value.trim() || phoneInput.value.trim();
            const password = qs("[data-role='new-password']", wrapper).value.trim();

            if (!password) { showMessage(wrapper.dataset.msgEnterNewPassword || 'رمز عبور جدید را وارد کنید', 'danger'); return; }

            if (password.length < 8) { showMessage('رمز عبور باید حداقل 8 کاراکتر باشد', 'danger'); return; }

            showMessage(wrapper.dataset.msgRegistering || 'در حال تغییر رمز عبور...', 'notice');
            setButtonLoading(btn,true);

            postData(loginOtpData.ajaxUrl, {
                action: 'login_reset_password',
                phone: phone,
                password: password,
                _ajax_nonce: loginOtpData.nonce
            })
            .then(res => {
                if (res.success) {
                    showMessage(wrapper.dataset.msgPasswordChanged || 'رمز عبور با موفقیت تغییر کرد', 'success');
                    const redirect = wrapper._state?.loginRedirect || res.data?.redirect || loginOtpData.redirect
                    setTimeout(() => window.location.href = redirect, 2000);
                } else {
                    showMessage(res.data?.message || wrapper.dataset.msgChangeFailed || 'تغییر رمز عبور ناموفق بود', 'danger');
                }
            })
            .catch(() => showMessage(wrapper.dataset.msgNetworkError || 'خطایی در ارتباط با سرور رخ داده است.', 'danger'))
            .finally(() => {
                    setButtonLoading(btn, false);
            });
        }

    });

    // AUTO VERIFY WHEN OTP LENGTH REACHED & ENABLE/DISABLE BUTTON
        document.addEventListener('input', function (e) {
            const input = e.target;
            // only care about inputs that are the OTP field itself
            if (input.getAttribute('data-role') !== 'otp') return;

            const wrapper = input.closest('.login-otp-wrapper');
            if (!wrapper) return;
            

            const otpInput = input;
            const digits = otpInput.value.replace(/\D/g, '');
            const length = wrapper._state?.otpLength || parseInt(wrapper.dataset.otpLength || ((typeof loginOtpData !== 'undefined' && loginOtpData.otpLength) ? loginOtpData.otpLength : 6), 10) || 6;

            // Find the step that contains this OTP input
            const step = otpInput.closest("[data-step]");
            if (!step) return;
            
            const stepName = step.getAttribute('data-step');
            
            // Get the verify button for this step 
            const verifyBtn = stepName === '2' 
                ? qs("[data-step='2'] [data-role='verify']", wrapper)
                : qs("[data-step='" + stepName + "'] [data-role='verify-forgot']", wrapper);
            
            if (verifyBtn) {
                // Enable/disable button based on OTP length
                if (digits.length >= length) {
                    verifyBtn.disabled = false;
                    verifyBtn.removeAttribute('aria-disabled');
                } else {
                    verifyBtn.disabled = true;
                    verifyBtn.setAttribute('aria-disabled', 'true');
                }

                // Auto-click when length is reached
                if (digits.length >= length) {
                    // debounce slightly to avoid racing with manual clicks or other handlers
                    setTimeout(() => {
                        const currentDigits = otpInput.value.replace(/\D/g, '');
                        if (currentDigits.length >= length) verifyBtn.click();
                    }, 100);
                }
            }
        });


        document.addEventListener('click', function (e) {
            const toggle = e.target.closest('[data-role="toggle-password"]');
            if (!toggle) return;

            const wrapper = toggle.closest('[data-role="password-wrapper"]');
            if (!wrapper) return;

            // works for password-field AND new-password
            const input = wrapper.querySelector('input[type="password"], input[type="text"]');
            if (!input) return;

            const isPassword = input.type === 'password';

            input.type = isPassword ? 'text' : 'password';
            toggle.classList.toggle('is-visible', isPassword);
        });

    // Initial load call
    document.addEventListener('DOMContentLoaded', () => {
        const wrapper = document.querySelector('.login-otp-wrapper');  // Get wrapper on load
        if (wrapper) {
            showStep(wrapper, '1');  // Or your initial step, e.g., 'password'
        } else {
            console.error('Wrapper not found on load');
        }
    });



})();