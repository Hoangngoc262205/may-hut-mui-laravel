<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | Here you may configure the third-party services for your application.
    |
    */

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'ghn' => [
        'base_url' => env('GHN_BASE_URL', 'https://online-gateway.ghn.vn'),
        'token' => env('GHN_TOKEN'),
        'shop_id' => env('GHN_SHOP_ID'),
        'verify_ssl' => env('GHN_VERIFY_SSL', true),

        'from_district_id' => env('GHN_FROM_DISTRICT_ID'),
        'service_type_id' => env('GHN_SERVICE_TYPE_ID', 2),
        'payment_type_id' => env('GHN_PAYMENT_TYPE_ID', 2),
        'required_note' => env('GHN_REQUIRED_NOTE', 'CHOXEMHANGKHONGTHU'),

        'from_name' => env(
            'GHN_FROM_NAME',
            env('APP_NAME', 'May Hut Mui Store')
        ),

        'from_phone' => env('GHN_FROM_PHONE', '0900000000'),
        'from_address' => env('GHN_FROM_ADDRESS', 'Dia chi kho hang'),
        'from_ward_name' => env('GHN_FROM_WARD_NAME', 'Phuong kho hang'),
        'from_district_name' => env('GHN_FROM_DISTRICT_NAME', 'Quan kho hang'),
        'from_province_name' => env('GHN_FROM_PROVINCE_NAME', 'Tinh kho hang'),

        'package' => [
            'height' => env('GHN_PACKAGE_HEIGHT', 20),
            'length' => env('GHN_PACKAGE_LENGTH', 60),
            'weight' => env('GHN_PACKAGE_WEIGHT', 5000),
            'width' => env('GHN_PACKAGE_WIDTH', 45),
        ],
    ],

    'momo' => [
        'endpoint' => env(
            'MOMO_ENDPOINT',
            'https://test-payment.momo.vn/v2/gateway/api/create'
        ),
        'partner_code' => env('MOMO_PARTNER_CODE'),
        'access_key' => env('MOMO_ACCESS_KEY'),
        'secret_key' => env('MOMO_SECRET_KEY'),
        'verify_ssl' => env('MOMO_VERIFY_SSL', true),
        'redirect_url' => env('MOMO_REDIRECT_URL'),
        'ipn_url' => env('MOMO_IPN_URL'),
    ],

];