<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;
use DI\ContainerBuilder;
use Slim\Routing\RouteCollectorProxy;
use Slim\Handlers\Strategies\RequestResponseArgs;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

define('APP_ROOT', dirname(__DIR__));

require APP_ROOT . '/vendor/autoload.php';

$builder = new ContainerBuilder;

$container = $builder->addDefinitions(APP_ROOT . '/config/definitions.php')->build();

AppFactory::setContainer($container);

$app = AppFactory::create();

$collector = $app->getRouteCollector();

$collector->setDefaultInvocationStrategy(new RequestResponseArgs());

$app->addBodyParsingMiddleware();
$app->group('/api', function(RouteCollectorProxy $group) {

    $group->get('/users', function(Request $req, Response $res){

    });


});

$app->run();