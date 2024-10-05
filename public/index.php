<?php

declare(strict_types=1);

use App\Controllers\Groups;
use App\Controllers\Messages;
use App\Controllers\Users;
use App\Middleware\AddJsonResponseHandler;
use App\Middleware\CorsMiddleware;
use App\Middleware\IsUserInGroup;
use App\Middleware\UserIdValidation;
use App\Middleware\ValidateGroupName;
use App\Middleware\ValidateToken;
use App\Middleware\ValidateUserInGroup;
use App\Middleware\ValidateUsername;
use App\Middleware\VerifyGroupIdExists;
use DI\ContainerBuilder;
use Slim\Factory\AppFactory;
use Slim\Handlers\Strategies\RequestResponseArgs;
use Slim\Routing\RouteCollectorProxy;

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

$app->add(new CorsMiddleware);

$app->group('/api', function (RouteCollectorProxy $group) {

    $group->group('/users', function (RouteCollectorProxy $group) {
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

    $group->group('/groups', function (RouteCollectorProxy $group) {
        $group->get('', [Groups::class, 'getAll'])->add(ValidateToken::class);

        $group->get('/{id}', [Groups::class, 'getById']);

        $group->post('', [Groups::class, 'create'])
            ->add(ValidateGroupName::class)
            ->add(ValidateToken::class);

        $group->patch('/{id}', [Groups::class, 'update'])
            ->add(ValidateUserInGroup::class)
            ->add(IsUserInGroup::class)
            ->add(ValidateGroupName::class)
            ->add(VerifyGroupIdExists::class)
            ->add(ValidateToken::class);

        $group->delete('/{id}', [Groups::class, 'delete'])
            ->add(ValidateUserInGroup::class)
            ->add(IsUserInGroup::class)
            ->add(VerifyGroupIdExists::class)
            ->add(ValidateToken::class);

        $group->post('/{id}/join', [Groups::class, 'join'])
            ->add(IsUserInGroup::class)
            ->add(VerifyGroupIdExists::class)
            ->add(ValidateToken::class);

        $group->post('/{id}/leave', [Groups::class, 'leave'])
            ->add(ValidateUserInGroup::class)
            ->add(IsUserInGroup::class)
            ->add(VerifyGroupIdExists::class)
            ->add(ValidateToken::class);

    });

    $group->group('/messages', function (RouteCollectorProxy $group) {
        $group->post('', [Messages::class, 'create'])
            ->add(ValidateUserInGroup::class)
            ->add(IsUserInGroup::class)
            ->add(VerifyGroupIdExists::class)
            ->add(ValidateToken::class);

        $group->delete('/{message_id}', [Messages::class, 'delete'])
            ->add(ValidateUserInGroup::class)
            ->add(IsUserInGroup::class)
            ->add(VerifyGroupIdExists::class)
            ->add(ValidateToken::class);

        $group->patch('/{message_id}', [Messages::class, 'update'])
            ->add(ValidateUserInGroup::class)
            ->add(IsUserInGroup::class)
            ->add(VerifyGroupIdExists::class)
            ->add(ValidateToken::class);

        $group->get('', [Messages::class, 'getAll'])
            ->add(ValidateUserInGroup::class)
            ->add(IsUserInGroup::class)
            ->add(VerifyGroupIdExists::class)
            ->add(ValidateToken::class);
    });


});

$app->run();