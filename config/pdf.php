<?php

return [
    'mode'                     => '',
    'format'                   => 'A4',
    'default_font_size'        => '12',
    'default_font'             => 'sans-serif',
    'margin_left'              => 0,
    'margin_right'             => 0,
    'margin_top'               => 0,
    'margin_bottom'            => 0,
    'margin_header'            => 2,
    'margin_footer'            => 0,
    'orientation'              => 'P',
    'title'                    => 'Laravel mPDF',
    'subject'                  => '',
    'author'                   => '',
    'watermark'                => '',
    'show_watermark'           => false,
    'show_watermark_image'     => false,
    'watermark_font'           => 'sans-serif',
    'display_mode'             => 'fullpage',
    'watermark_text_alpha'     => 0.1,
    'watermark_image_path'     => '',
    'watermark_image_alpha'    => 0.2,
    'watermark_image_size'     => 'D',
    'watermark_image_position' => 'P',
    'auto_language_detection'  => false,
    'temp_dir'                 => storage_path('app'),
    'pdfa'                     => false,
    'pdfaauto'                 => false,
    'use_active_forms'         => false,
    'font_path' => base_path('resources/fonts/'),
    'font_data' => [
        'serif' => [
            'R' => 'NotoSerif-Regular.ttf',
            'B' => 'NotoSerif-Bold.ttf'
        ],
        'notosanthai' => [
            'R' => 'NotoSansThai-Regular.ttf'
        ],
        'pyidaungsu' => [
            'R' => 'Tharlon-Regular.ttf',
            'useOTL' => 0xFF,
        ],
        'default_font' => 'pyidaungsu',
        // ...add as many as you want.
    ]
];
