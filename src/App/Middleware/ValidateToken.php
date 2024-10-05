<?php

namespace App\Middleware;

use App\Repositories\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;
use Slim\Routing\RouteContext;

class ValidateToken
{

    public function __construct(private UserRepository $repository)
    {
    }

    public function __invoke(Request $req, RequestHandler $handler): Response
    {
        $context = RouteContext::fromRequest($req);

        $route = $context->getRoute();

        $id = $route->getArgument('id');

        $token = $req->getHeaderLine('Authorization');

        if (empty($token)) {
            $response = new SlimResponse();
            $response->getBody()->write(json_encode(['Error' => 'Token not provided']));
            return $response->withStatus(401);
        }

        $user = $this->repository->getToken($token);

        if ($user === false) {
            $response = new SlimResponse();
            $response->getBody()->write(json_encode(['Error' => 'User validation failed']));
            return $response->withStatus(401);
        }
        $req = $req->withAttribute('user_id', $user['id']);

        return $handler->handle($req);
    }
}
