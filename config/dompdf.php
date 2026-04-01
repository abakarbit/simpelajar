<?php

return [
    'mode'                  => 'utf-8',
    'format'                => 'A4',
    'default_font'          => 'sans-serif',
    'font_path'             => base_path('resources/fonts/'),
    'font_cache'            => storage_path('fonts/'),
    'temp_dir'              => storage_path('app/dompdf_temp/'),
    'chroot'                => base_path(),
    'logOutputFile'         => null,
    'defaultMediaType'      => 'print',
    'defaultPaperSize'      => 'A4',
    'margin_top'            => 0,
    'margin_right'          => 0,
    'margin_bottom'         => 0,
    'margin_left'           => 0,
    'Attachment'            => false,
    'enable_font_subsetting' => true,
    'pdf_rendering_backend' => 'auto',
    'javascript'            => true,
    'enable_remote'         => false,
    'public_path'           => null,
    'white_list'            => [
        base_path('vendor'),
        base_path('resources'),
        base_path('storage'),
    ],
];
