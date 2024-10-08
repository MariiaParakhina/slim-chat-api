<?php

use Ratchet\App;
use App\Chat;

define('APP_ROOT', dirname(__DIR__));

require APP_ROOT . '/vendor/autoload.php';

$app = new App('localhost', 8083);

$app->route('/chat', new Chat, ['*']);

$app->run();