<?php
Config::set('school', [
    'active' => true,
    'path' => 'modules',
    'topmenu' => true,
    "dependencies" => [
        "tecnickcom/tcpdf" => "^6.2.13"
    ]
]);