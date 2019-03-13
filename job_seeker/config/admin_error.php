<?php

return [
    'no_record_found' => [
        'status' => 400, 'message' => 'No record found.','data'=> []
    ],
    'not_created' => [
        'status' => 400, 'message' => 'Data not able to insert.'
    ],
    'not_deleted' => [
        'status' => 400, 'message' => 'Data not able to delete.'
    ],
    'unauthorised' => [
        'status' => 400, 'message' => 'Unauthorised Action.'
    ],
    'not_varified' => [
        'status' => 400, 'message' => 'Your account is not verified, please verify.'
    ],
    'invalid_credentials' => [
        'status' => 400, 'message' => 'Invalid email/password.'
    ],  
    'error' => [
        'status' => 400, 'message' => 'Error.'
    ],  
    'mail_error' => [
        'status' => 400, 'message' => 'There is an error while sending email.'
    ],
    'password_update_error' => [
        'status' => 400, 'message' => 'There was an error while updating password.'
    ],
    'invalid_old_password' => [
        'status' => 400, 'message' => 'Please enter valid old password.'
    ],  
    'eamil_failed' => [
        'status' => 400, 'message' => 'Email sending failed.',
    ]
    
    
];