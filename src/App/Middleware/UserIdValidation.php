<?php
namespace App\Middleware;

use App\Repositories\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;
use Slim\Routing\RouteContext;

class UserIdValidation
{

    public function __construct(private UserRepository $repository)
    {
    }
    public function __invoke(Request $req,  RequestHandler $handler): Response
    {
        $user = $req->getAttribute("user");

        $context = RouteContext::fromRequest($req);

        $route = $context->getRoute();

        $user_id = $route->getArgument('id');

        if($user_id !== $user['id']){
            $response = new SlimResponse();
            $response->getBody()->write(json_encode(['Error' => 'Failed user validation, you do not have rights to manage this data']));
            return $response->withStatus(409);
        }


        return $handler->handle($req);
    }
}
