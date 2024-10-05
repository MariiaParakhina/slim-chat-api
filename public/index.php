<?php

declare(strict_types=1);

use App\Middleware\AddJsonResponseHandler;
use Slim\Factory\AppFactory;
use DI\ContainerBuilder;
use Slim\Routing\RouteCollectorProxy;
use Slim\Handlers\Strategies\RequestResponseArgs;
use App\Controllers\Users;
use App\Middleware\UsernameVerification;
use App\Middleware\UserVerification;

define('APP_ROOT', dirname(__DIR__));

require APP_ROOT . '/vendor/autoload.php';

$builder = new ContainerBuilder;

$container = $builder->addDefinitions(APP_ROOT . '/config/definitions.php')->build();

AppFactory::setContainer($container);

$app = AppFactory::create();

$collector = $app->getRouteCollector();

$collector->setDefaultInvocationStrategy(new RequestResponseArgs());

$app->addBodyParsingMiddleware();

$error_middleware = $app->addErrorMiddleware(true, true, true);

$error_handler = $error_middleware->getDefaultErrorHandler();

$error_handler->forceContentType("application/json");

$app->add(new AddJsonResponseHandler);

$app->group('/api', function(RouteCollectorProxy $group) {

    $group->get('/users', [Users::class, 'getAll']);
    $group->get('/users/{id}', [Users::class, 'getById']);
    $group->post('/users', [Users::class, 'create'])->add(UsernameVerification::class);

    $group->patch('/users/{id}', [Users::class, 'update'])->add(UserVerification::class);
    $group->delete('/users/{id}', [Users::class, 'delete'])->add(UserVerification::class);

});

$app->run();