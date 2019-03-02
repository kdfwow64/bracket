<?php

return [
    'apple' => [
        'sandbox' => [
            'url' => 'ssl://gateway.sandbox.push.apple.com:2195',
            'pem_file' => base_path('pem') . '/bracket.pem',
            'passphrase' => ''
        ], 'production' => [
            'url' => 'ssl://gateway.push.apple.com:2195',
            'pem_file' => base_path('pem') . '/bracket.pem',
            'passphrase' => ''
        ]
    ]
];
