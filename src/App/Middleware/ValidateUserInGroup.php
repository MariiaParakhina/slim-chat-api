<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

class ValidateUserInGroup
{

    public function __invoke(Request $req, RequestHandler $handler): Response
    {
        $is_user_in_group = $req->getAttribute('is_user_in_group');

        if (!$is_user_in_group) {
            $res = new SlimResponse();
            $res->getBody()->write(json_encode(["Error" => "User is not in this group"]));
            return $res->withStatus(422);
        }
        return $handler->handle($req);
    }
}
