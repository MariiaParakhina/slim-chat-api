<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\UserRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;
use Slim\Routing\RouteContext;
use Valitron\Validator;

class Users
{
    public function __construct(private UserRepository $repository)
    {
    }
    public function getAll(Request $req, Response $res):Response
    {

        $data = $this->repository->getAll();

        $body = json_encode($data);

        $res->getBody()->write($body);

        return $res;
    }
    public function getById(Request $req, Response $res, string $id): Response
    {
        $data = $this->repository->getById((int) $id);

        if($data === false){
            throw new HttpNotFoundException($req);
        }

        $body = json_encode($data);

        $res->getBody()->write($body);

        return $res;
    }

    public function create(Request $req, Response $res): Response
    {
        $body = $req->getParsedBody();



        $username = $body['username'];

        $token = bin2hex(random_bytes(16));

        $id = $this->repository->create($username, $token);

        $responseBody = json_encode([
            'message' => 'User created',
            'id' => $id,
            'token'=> $token
        ]);

        $res->getBody()->write($responseBody);

        return $res->withStatus(201);
    }

    public function update(Request $req, Response $res, string $id): Response
    {
        $body = $req->getParsedBody();
        $username = $body['username'];

        $rows = $this->repository->update((int) $id, $username);

        $responseBody = json_encode([
            'message' => 'Username updated',
            'rows' => $rows,
        ]);

        $res->getBody()->write($responseBody);

        return $res;
    }

    public function delete(Request $req, Response $res, string $id): Response
    {
        $rows = $this->repository->delete((int)$id);

        $responseBody = json_encode([
            'message' => 'User deleted',
            'rows' => $rows,]);

        $res->getBody()->write($responseBody);

        return $res;
    }
}
