<?php

require __DIR__ . '/../vendor/autoload.php';


use metrica\core\Bootstrap;

$di = Bootstrap::di();
$di->get('router')->get('/', function() {
    return 'Hello World';
});
