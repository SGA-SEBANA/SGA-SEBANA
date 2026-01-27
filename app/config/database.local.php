<?php

return [
    'host' => $_ENV['DB_LOCAL_HOST'],
    'port' => $_ENV['DB_LOCAL_PORT'],
    'dbname' => $_ENV['DB_LOCAL_DATABASE'],         
    'username' => $_ENV['DB_LOCAL_USERNAME'],
    'password' => $_ENV['DB_LOCAL_PASSWORD'],                   
    'charset' => $_ENV['DB_LOCAL_CHARSET'],
    'options' => [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]
];