<?php
namespace App\Middleware;

use App\Repositories\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;
use Slim\Routing\RouteContext;

class UsernameVerification
{

    public function __construct(private UserRepository $repository)
    {
    }
    public function __invoke(Request $req,  RequestHandler $handler): Response
    {
        $body = $req->getParsedBody();

        $username = $body['username'];

        $data = $this->repository->getByUsername($username);

        if($data !== false){
            $response = new SlimResponse();
            $response->getBody()->write(json_encode(['Error' => 'Username already exists']));
            return $response->withStatus(409);
        }

        return $handler->handle($req);
    }
}
