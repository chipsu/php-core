# Another "lightweight" PHP-Framework

Because why not.

## Installation

`composer require metrica/php-core`

## Features

- No external dependencies (except for tests)
- Simple pattern or regex based routing
- DI container
- Basic event system

## How do I use this?

Take a look in the examples folder.

```
<?php

require 'vendor/autoload.php';

using metrica\core\Bootstrap;

$di = Bootstrap::di()
$di->get('router').on(['GET'], '/', function($request) {
  return ['message' => 'Hello World!'];
});
$di->get('kernel')->run();
```
