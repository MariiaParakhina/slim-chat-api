<?php

namespace App\Middleware;

use App\Repositories\GroupRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;
use Slim\Routing\RouteContext;

class ValidateUserInGroup {

    public function __invoke(Request $req, RequestHandler $handler): Response {
        $is_user_in_group = $req->getAttribute('is_user_in_group');

        if(!$is_user_in_group){
            $res = new SlimResponse();
            $res->getBody()->write(json_encode(["Error"=>"User is not in this group"]));
            return $res->withStatus(422);
        }
        return $handler->handle($req);
    }
}
