<?php

return [
    //Disable this one for Local
    'is_mail_send' => env('MAIL_NOTIFICATION_SEND', false),
    'post_prefix'   => 'post',
    'comment_prefix'   => 'comment',
    'users' => [
        'default_user_type' => 1,
        'staff_user_type' => 2,
        'participant_user_type' => 3
    ],
    'automation_user' => env('AUTOMATION_USER', '.com'),
    'general_status_option' => [1 => 'Active', 2 => 'Inactive', 3 => 'Archive'],
    'STAFF' => [
        'STAFF_STATUS_OPTION' => [1 => 'Active', 2 => 'Inactive', 3 => 'Archive'],
    ],
    'STATUS_VALUE' => [
        'ACTIVE' => 1,
        'INACTIVE' => 2,
        'ARCHIVE' => 3
    ],
    'OTP_EXPIRY_IN_SEC' => env('OTP_EXPIRY_IN_SEC', 180),
    'ACCEPTED_SECRETS' => env('ACCEPTED_SECRETS', 'base+KwzE='),
    'EMAIL_EXPIRY_IN_HOURS' => env('EMAIL_EXPIRY_IN_HOURS', 4),
    'CUSTOM_ERROR_MESSSAGE' => [
        'CATCH' => 'Something went wrong. please try again',
    ],
];
