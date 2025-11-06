<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Mood Options
    |--------------------------------------------------------------------------
    |
    | Available mood emojis for diary entries.
    |
    */
    'moods' => ['😊', '😍', '😎', '😌', '😴', '😢', '😤', '🤔'],

    /*
    |--------------------------------------------------------------------------
    | Theme Options
    |--------------------------------------------------------------------------
    |
    | Available color themes for the application.
    |
    */
    'themes' => ['purple', 'pink', 'blue', 'green', 'orange'],

    /*
    |--------------------------------------------------------------------------
    | Image Settings
    |--------------------------------------------------------------------------
    |
    | Maximum number of images per diary entry and maximum file size in KB.
    |
    */
    'max_images_per_entry' => 10,
    'image_max_size' => 5120, // 5MB in KB

    /*
    |--------------------------------------------------------------------------
    | Storage Paths
    |--------------------------------------------------------------------------
    |
    | Storage paths for photos and thumbnails.
    |
    */
    'photos_path' => 'photos',
    'thumbnails_path' => 'thumbnails',
];

