<?php


return [
    'temporary_file_upload' => [
        'disk'      => 'public',
        'rules'     => ['image', 'max:2048'], // max 2MB
        'directory' => 'tmp',
    ],
];