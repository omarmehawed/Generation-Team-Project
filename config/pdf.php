<?php

return [
	'mode'                  => 'utf-8',
	'format'                => 'A4',
	'author'                => '',
	'subject'               => '',
	'keywords'              => '',
	'creator'               => 'Laravel Pdf',
	'display_mode'          => 'fullpage',
	'tempDir'               => base_path('storage/app/mpdf'),
	'pdf_a'                 => false,
	'pdf_a_auto'            => false,
	'icc_profile_path'      => '',
    'custom_font_dir' => base_path('public/fonts/'),
    'custom_font_data' => [
        'amiri' => [
            'R'  => 'Amiri-Regular.ttf',    // regular font
            'useOTL' => 0,
            'useKashida' => 75,
        ]
    ],
    'auto_language_detection' => true,
    'temp_dir' => base_path('storage/app/mpdf'),
];
