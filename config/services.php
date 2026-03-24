<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
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

    /*
    |--------------------------------------------------------------------------
    | VNPay Sandbox — Cổng thanh toán test
    |--------------------------------------------------------------------------
    | Đăng ký tài khoản merchant test tại: https://sandbox.vnpayment.vn
    | Thẻ test (NCB): 9704198526191432198, tên NGUYEN VAN A, ngày 07/15
    */
    'vnpay' => [
        'url'         => env('VNP_URL', 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html'),
        'tmn_code'    => env('VNP_TMN_CODE', 'W9WBI93N'),
        'hash_secret' => env('VNP_HASH_SECRET', 'I2LOZSYVLFOAV5WR5FKREFQ6VIZHRY7B'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Momo Sandbox — Cổng thanh toán test
    |--------------------------------------------------------------------------
    | Đăng ký tài khoản dev tại: https://business.momo.vn
    | Dùng app Momo Test để quét QR thanh toán giả lập.
    */
    'momo' => [
        'partner_code' => env('MOMO_PARTNER_CODE', 'MOMO'),
        'access_key'   => env('MOMO_ACCESS_KEY', 'F8BBA842ECF85'),
        'secret_key'   => env('MOMO_SECRET_KEY', 'K951B6PE1waDMi640xX08PD3vg6EkVlz'),
        'endpoint'     => env('MOMO_ENDPOINT', 'https://test-payment.momo.vn/v2/gateway/api/create'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Maps API — Tính phí vận chuyển
    |--------------------------------------------------------------------------
    */
    'google_maps' => [
        'api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Shipping — Cấu hình phí vận chuyển
    |--------------------------------------------------------------------------
    | base_fee: phí cơ bản (VNĐ)
    | per_km_fee: phí mỗi km (VNĐ)
    | max_fee: phí tối đa (VNĐ)
    | free_above: miễn phí khi đơn >= X (VNĐ), 0 = không miễn phí
    | shop_latitude/longitude: tọa độ cửa hàng gốc
    */
    'shipping' => [
        'base_fee'       => env('SHIPPING_BASE_FEE', 15000),
        'per_km_fee'     => env('SHIPPING_PER_KM_FEE', 5000),
        'max_fee'        => env('SHIPPING_MAX_FEE', 100000),
        'free_above'     => env('SHIPPING_FREE_ABOVE', 500000),
        'shop_latitude'  => env('SHOP_LATITUDE', 10.762622),
        'shop_longitude' => env('SHOP_LONGITUDE', 106.660172),
    ],

];
