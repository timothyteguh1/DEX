<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Configuration
    |--------------------------------------------------------------------------
    |
    | An array of Cloudinary API configuration variables.
    |
    */

    'cloud_url' => env('CLOUDINARY_URL'),

    /*
    |--------------------------------------------------------------------------
    | Cloudinary Upload Preset
    |--------------------------------------------------------------------------
    |
    | If you want to use unsigned uploads, set this variable.
    |
    */

    'upload_preset' => env('CLOUDINARY_UPLOAD_PRESET'),

    /*
    |--------------------------------------------------------------------------
    | Cloudinary URL properties
    |--------------------------------------------------------------------------
    |
    | If you want to configure default properties for generated URLs.
    |
    */

    'url' => [
        'secure' => true,
    ],

];