<?php

namespace App\Middleware;

use App\Repositories\GroupRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;
use Slim\Routing\RouteContext;

class ValidateUserInGroup{
    public function __construct(private GroupRepository $repository)
    {
    }
    public function __invoke(Request $req,  RequestHandler $handler): Response
    {
        $context = RouteContext::fromRequest($req);

        $route = $context->getRoute();

        $id = $route->getArgument('id');

        $user_id = $req->getAttribute('user_id');

        $is_user_in_group = $this->repository->isUserInGroup((int) $id, (int) $user_id);

        $req = $req->withAttribute("is_user_in_group", $is_user_in_group);


        return $handler->handle($req);
    }
}