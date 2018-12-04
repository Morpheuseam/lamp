<?php

session_start();

header('Content-Type: application/json');

require '../vendor/autoload.php';

$config = [
    'settings' => [
        'displayErrorDetails' => true
    ]
];

function __autoload($class) {
    require '../model/' . $class . '.php';
}

$app = new \Slim\App($config);

require_once '../middlewares/mid01.php';

require_once 'post.php'; // routes post


$app->run();
