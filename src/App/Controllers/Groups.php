<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\GroupRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Groups
{
    public function __construct(private GroupRepository $repository)
    {
    }

    public function getAll(Request $req, Response $res): Response
    {
        $data = $this->repository->getAll();

        $res->getBody()->write(json_encode($data));

        return $res;
    }

    public function getById(Request $req, Response $res, string $id): Response
    {
        $data = $this->repository->getById((int)$id);

        $body = json_encode($data);

        $res->getBody()->write($body);

        return $res;
    }

    public function create(Request $req, Response $res): Response
    {
        $body = $req->getParsedBody();

        $user_id = $req->getAttribute('user_id');

        $name = $body['name'];

        $id = $this->repository->create($name);

        $rows = $this->repository->addUserToGroup((int)$id, (int)$user_id);

        $responseBody = json_encode([
            'message' => 'Group created',
            'id' => $id,
            'note' => "You have been added to the group",
            'rows affected' => $rows
        ]);

        $res->getBody()->write($responseBody);

        return $res->withStatus(201);
    }

    public function join(Request $req, Response $res, string $id): Response
    {
        $user_id = $req->getAttribute('user_id');

        $is_user_in_group = $req->getAttribute('is_user_in_group');

        if ($is_user_in_group) {
            $res->getBody()->write(json_encode(["Error" => "User is already in a group"]));
            return $res->withStatus(422);
        }

        $rows = $this->repository->addUserToGroup((int)$id, (int)$user_id);

        $responseBody = json_encode([
            'message' => 'you have been added to the group',
            'rows affected' => $rows,
        ]);

        $res->getBody()->write($responseBody);

        return $res;
    }

    public function leave(Request $req, Response $res, string $id): Response
    {
        $user_id = $req->getAttribute('user_id');

        $rows = $this->repository->deleteUserFromGroup((int)$id, (int)$user_id);

        $responseBody = json_encode([
            'message' => 'User have left the group',
            'rows affected' => $rows,

        ]);

        $res->getBody()->write($responseBody);

        return $res;
    }

    public function delete(Request $req, Response $res, string $id): Response
    {
        $rows = $this->repository->delete((int)$id);

        $responseBody = json_encode([
            'message' => 'Group was deleted',
            'rows' => $rows,]);

        $res->getBody()->write($responseBody);

        return $res;
    }

    public function update(Request $req, Response $res, string $id): Response
    {
        $body = $req->getParsedBody();
        $username = $body['name'];

        $rows = $this->repository->update((int)$id, $username);

        $responseBody = json_encode([
            'message' => 'Group name was updated',
            'rows' => $rows,
        ]);

        $res->getBody()->write($responseBody);

        return $res;
    }

}