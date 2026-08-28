<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines are used during authentication for various
    | messages that we need to display to the user. You are free to modify
    | these language lines according to your application's requirements.
    |
    */

    'failed'   => 'These credentials do not match our records.',
    'throttle' => 'Too many login attempts. Please try again in :seconds seconds.',

    'full_name'        => 'Full Name',
    'email'            => 'Email',
    'password'         => 'Password',
    'confirm_password' => 'Confirm Password',
    'remember_me'      => 'Remember Me',
    'sign_in'          => 'Sign In',
    'sign_out'         => 'Sign out',
    'register'         => 'Register',

    'login' => [
        'title'               => 'Sign in to start your session',
        'forgot_password'     => 'I forgot my password',
    ],

    'registration' => [
        'title'           => 'Register a new membership',
        'i_agree'         => 'I agree to',
        'terms'           => 'the terms',
        'have_membership' => 'I already have a membership',
    ],

    'forgot_password' => [
        'title'          => 'You forgot your password? Here you can easily retrieve a new password.',
        'send_pwd_reset' => 'Send Password Reset Link',
    ],

    'reset_password' => [
        'title'         => 'You are only one step a way from your new password, recover your password now.',
        'reset_pwd_btn' => 'Reset Password',
    ],

    'confirm_passwords' => [
        'title'                => 'Please confirm your password before continuing.',
        'forgot_your_password' => 'Forgot Your Password?',
    ],

    'verify_email' => [
        'title'       => 'Verify Your Email Address',
        'success'     => 'A fresh verification link has been sent to your email address',
        'notice'      => 'Before proceeding, please check your email for a verification link.If you did not receive the email,',
        'another_req' => 'click here to request another',
    ],

    'emails' => [
        'password' => [
            'reset_link' => 'Click here to reset your password',
        ],
    ],



    'registration_successful' => 'Registration successful! Please check your email for verification.',
    'email.required' => 'The email field is required.',
    'email.email' => 'The email must be a valid email address.',
    'email.unique' => 'This email is already taken.',
    'phone.required' => 'The phone field is required.',
    'password.required' => 'The password field is required.',
    'password.min' => 'The password must be at least 6 characters.',
    'password.confirmed' => 'The password confirmation does not match.',
     'login_successful' => 'Login successful!',

    'otp_sent'=>'OTP code sent successfully',
    'otp_fail'=>'Failed to send verification code. Please try again.',
    'otp_Invalid'=>'Invalid OTP code',
    'otp_expired'=>'Verification code has expired',
    'otp_valid'=>'Verification code is valid',
     'pass_reset'=>'Password reset successfully',
     'old_pass_fail'=>'Current password is incorrect',
     'newpass_fail'=>'New password cannot be the same as the current password',
     'pass_changed'=>'Password changed successfully',
     'account_inactive' => 'Your account is inactive. Please contact administration.',
     'invalid_credentials' => 'Invalid login credentials.'
];
