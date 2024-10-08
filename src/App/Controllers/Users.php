<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpNotFoundException;

class Users
{
    public function __construct(private UserRepository $repository)
    {
    }

    public function getAll(Request $req, Response $res): Response
    {
        $data = $this->repository->getAll();

        return $this->jsonResponse($res, $data);
    }

    public function getById(Request $req, Response $res, string $id): Response
    {
        $data = $this->repository->getById((int)$id);

        if ($data === false) {
            throw new HttpNotFoundException($req);
        }

        return $this->jsonResponse($res, $data);
    }

    public function create(Request $req, Response $res): Response
    {
        $body = $req->getParsedBody();

        $username = $body['username'];

        $token = bin2hex(random_bytes(16));

        $id = $this->repository->create($username, $token);

        return $this->jsonResponse($res, [
            'message' => 'User created',
            'id' => $id,
            'token' => $token
        ], 201);
    }

    public function update(Request $req, Response $res, string $id): Response
    {
        $body = $req->getParsedBody();

        $username = $body['username'];

        $rows = $this->repository->update((int)$id, $username);

        return $this->jsonResponse($res, [
            'message' => 'Username updated',
            'rows' => $rows,
        ]);
    }

    public function delete(Request $req, Response $res, string $id): Response
    {
        $rows = $this->repository->delete((int)$id);

        return $this->jsonResponse($res, [
            'message' => 'User deleted',
            'rows' => $rows,
        ]);
    }

    private function jsonResponse(Response $res, array $data, int $status = 200): Response
    {
        $res->getBody()->write(json_encode($data));

        return $res->withStatus($status);
    }
}