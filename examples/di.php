<?php

class Database
{

}

$depends = new Depends;
$depends->add('depends', $depends);
$depends->add(RequestInterface::class, Request::class);
$depends->add(RequestInterface::class, Request::class, 'request');
$depends->add('db', Database::class);
$depends->get('db');

$depends->add('request', Request::class);
$depends->addConstructor('request', fn(Env $env) => Request::fromEnv($env));

$depends->create('db');
