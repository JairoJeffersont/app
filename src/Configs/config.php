<?php
return [

    'database' => [
        'host' => 'localhost',
        'name' => 'gabinete_dig',
        'user' => 'gabinete_dig',
        'password' => 'Intell@3103',
    ],

    'master_user' => [
        'master_name' => 'Administrador',
        'master_email' => 'admin@admin.com',
        'master_pass' => 'intell01',
    ],
    
    'app' => [
        'session_time' => 24,
        'base_url' =>rtrim($_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']) . '/', '')
    ]
];