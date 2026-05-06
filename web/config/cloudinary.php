<?php

return [

    'notification_url' => env('CLOUDINARY_NOTIFICATION_URL'),

    // 🔥 TAMBAHKAN INI (INI KUNCI UTAMA)
    'cloud' => [
        'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
        'api_key'    => env('CLOUDINARY_API_KEY'),
        'api_secret' => env('CLOUDINARY_API_SECRET'),
    ],

    'url' => [
        'secure' => true,
    ],

    // ini boleh tetap ada
    'cloud_url' => env('CLOUDINARY_URL'),

    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET'),

    'upload_route' => env('CLOUDINARY_UPLOAD_ROUTE'),

    'upload_action' => env('CLOUDINARY_UPLOAD_ACTION'),
];