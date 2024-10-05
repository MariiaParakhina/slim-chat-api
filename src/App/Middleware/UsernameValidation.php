<?php

namespace App\Middleware;

use App\Repositories\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;
use Valitron\Validator;

class UsernameValidation
{

    public function __construct(private UserRepository $repository)
    {
    }
    public function __invoke(Request $req,  RequestHandler $handler): Response
    {
        $body = $req->getParsedBody();

        $validator = new Validator($body);

        $validator->rule('required', 'username');

        if (!$validator->validate()) {
            $res = new SlimResponse();
            $res->getBody()->write(json_encode($validator->errors()));
            return $res->withStatus(422);
        }

        $username = $body['username'];

        $data = $this->repository->getByUsername($username);

        if($data !== false){
            $res = new SlimResponse();
            $res->getBody()->write(json_encode(['Error' => 'Username already exists']));
            return $res->withStatus(403);
        }

        return $handler->handle($req);
    }
}
