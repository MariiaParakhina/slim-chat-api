<?php

namespace App\Middleware;

use App\Repositories\GroupRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;
use Slim\Routing\RouteContext;
use Valitron\Validator;

class VerifyGroupIdExists{
    public function __construct(private GroupRepository $repository)
    {
    }
    public function __invoke(Request $req,  RequestHandler $handler): Response
    {
        $context = RouteContext::fromRequest($req);

        $route = $context->getRoute();

        $id = $route->getArgument('id');

        $data = $this->repository->getById((int)$id);

        if($data === False){
            $res = new SlimResponse();
            $res->getBody()->write(json_encode(['Error' => 'This group does not exists']));
            return $res->withStatus(422);
        }

        return $handler->handle($req);
    }
}