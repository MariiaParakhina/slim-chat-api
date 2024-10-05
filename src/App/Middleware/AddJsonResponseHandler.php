<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class AddJsonResponseHandler
{
    public function __invoke(Request $req, RequestHandler $handler): Response
    {
        $res = $handler->handle($req);

        return $res->withHeader('Content-Type', 'application/json');
    }
}