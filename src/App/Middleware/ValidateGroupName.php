<?php

namespace App\Middleware;


use App\Repositories\GroupRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;
use Valitron\Validator;

class ValidateGroupName
{
    public function __construct(private GroupRepository $repository)
    {
    }

    public function __invoke(Request $req, RequestHandler $handler): Response
    {
        $body = $req->getParsedBody();

        $validator = new Validator($body);

        $validator->rule('required', 'name');

        if (!$validator->validate()) {
            $res = new SlimResponse();
            $res->getBody()->write(json_encode($validator->errors()));
            return $res->withStatus(422);
        }

        $name = $body['name'];

        $data = $this->repository->getByName($name);

        if ($data !== false) {
            $res = new SlimResponse();
            $res->getBody()->write(json_encode(['Error' => 'This group name is already taken']));
            return $res->withStatus(422);
        }


        return $handler->handle($req);

    }

}