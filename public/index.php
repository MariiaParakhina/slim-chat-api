<?php

declare(strict_types=1);

use App\Controllers\Groups;
use App\Middleware\AddJsonResponseHandler;
use Slim\Factory\AppFactory;
use DI\ContainerBuilder;
use Slim\Routing\RouteCollectorProxy;
use Slim\Handlers\Strategies\RequestResponseArgs;
use App\Controllers\Users;
use App\Middleware\ValidateUsername;
use App\Middleware\ValidateToken;
use App\Middleware\UserIdValidation;
use App\Middleware\ValidateGroupName;
use App\Middleware\ValidateUserInGroup;
use App\Middleware\VerifyGroupIdExists;


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

    $group->group('/users', function(RouteCollectorProxy $group)
    {
        $group->get('', [Users::class, 'getAll']);

        $group->get('/{id}', [Users::class, 'getById']);

        $group->post('', [Users::class, 'create'])
            ->add(ValidateUsername::class);

        $group->patch('/{id}', [Users::class, 'update'])
            ->add(ValidateUsername::class)
            ->add(ValidateToken::class);

        $group->delete('/{id}', [Users::class, 'delete'])
            ->add(UserIdValidation::class)
            ->add(ValidateToken::class);
    });
    
    $group->group('/groups', function(RouteCollectorProxy $group)
    {
        $group->get('', [Groups::class, 'getAll'])->add(ValidateToken::class);

        $group->get('/{id}', [Groups::class, 'getById']);

        $group->post('', [Groups::class, 'create'])
            ->add(ValidateGroupName::class)
            ->add(ValidateToken::class);

        $group->patch('/{id}', [Groups::class, 'update'])
            ->add(ValidateGroupName::class)
            ->add(VerifyGroupIdExists::class)
            ->add(ValidateToken::class);

        $group->delete('/{id}', [Groups::class, 'delete'])
            ->add(VerifyGroupIdExists::class)
            ->add(ValidateToken::class);

        $group->post('/{id}/join', [Groups::class, 'join'])
            ->add(ValidateUserInGroup::class)
            ->add(VerifyGroupIdExists::class)
            ->add(ValidateToken::class);

        $group->post('/{id}/leave', [Groups::class, 'leave'])
            ->add(VerifyGroupIdExists::class)
            ->add(ValidateUserInGroup::class)
            ->add(ValidateToken::class);

    });

    $group->group('/messages', function(RouteCollectorProxy $group) {
      //  $group->post('', [Messages::class, 'create'])->add(ValidateToken::class);

    });




});

$app->run();