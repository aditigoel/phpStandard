<?php

return [

    'user_created' => [
        'status' => 400, 'message' => 'Error in sign up.Please try again later.',
    ],
    'account_not_verified' => [
        'status' => 500, 'message' => 'Your account is not verified, please verify.',
    ],
    'account_blocked_admin' => [
        'status' => 400, 'message' => 'Your account has been blocked by the admin, please contact admin.',
    ],
    'invalid_email_password' => [
        'status' => 400, 'message' => 'Email or password is incorrect.',
    ],
    'resend_verification' => [
        'status' => 400, 'message' => 'Error in send verification code .please try again later .',
    ],
    'send_forgot_password_link' => [
        'status' => 400, 'message' => 'Error in create forgot password token.please try again later .',
    ],
    
    'reported_user' => [
        'status' => 400, 'message' => 'Error in reporting user',
    ],
    
    'update_password' => [
        'status' => 400, 'message' => 'Error in update',
    ],
    'wrong_old_password' => [
        'status' => 400, 'message' => 'Old password is incorrect.',
    ],
    'get_personal_profile' => [
        'status' => 400, 'message' => 'Error in fetching profile ',
    ],
    'update_personal_profile' => [
        'status' => 400, 'message' => 'Error in update ',
    ],
    'invalid_file_format' => [
        'status' => 400, 'message' => 'Invalid file format. Allowed format : jpg,jpeg,png ',
    ],
    'file_too_large' => [
        'status' => 400, 'message' => 'File too large. Max upload size 2MB ',
    ],
    'uploaded_profile_image' => [
        'status' => 400, 'message' => 'Error in uploading file',
    ],
    
    'action_on_off' => [
        'status' => 400, 'message' => 'Error in changing action',
    ],
    'get_pages' => [
        'status' => 400, 'message' => 'Error in fetching pages',
    ],
    'error_sending_email' => [
        'status' => 400, 'message' => 'Error in sending email',
    ],
    'update_email' => [
        'status' => 400, 'message' => 'Error in update',
    ],
    'wrong_old_email' => [
        'status' => 400, 'message' => 'Old email is incorrect',
    ],
    'error_email_not_send' => [
        'status' => 400, 'message' => 'email not send',
    ],
    'error_token_no_saved' => [
        'status' => 400, 'message' => 'error_token_no_saved',
    ],
    'blocked_user' => [
        'status' => 400, 'message' => 'error in blocked user',
    ],
     'reported_user' => [
        'status' => 400, 'message' => 'error in reported user',
    ],
    'list_reported_user' => [
        'status' => 400, 'message' => 'error in reported users list',
    ],
     'unreport_user' => [
        'status' => 400, 'message' => 'error in un report user',
    ],
     'list_blocked_user' => [
        'status' => 400, 'message' => 'error in blocked users list',
    ],
     'unblock_user' => [
        'status' => 400, 'message' => 'error in un block user',
    ],
    
    
];
