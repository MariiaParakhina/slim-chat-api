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

        return $this->jsonResponse($res, $data);
    }

    public function getById(Request $req, Response $res, string $id): Response
    {
        $data = $this->repository->getById((int)$id);

        return $this->jsonResponse($res, $data);
    }

    public function create(Request $req, Response $res): Response
    {
        $body = $req->getParsedBody();

        $user_id = $req->getAttribute('user_id');

        $name = $body['name'];

        $id = $this->repository->create($name);

        $rows = $this->repository->addUserToGroup((int)$id, (int)$user_id);

        return $this->jsonResponse($res, [
            'message' => 'Group created',
            'id' => $id,
            'note' => "You have been added to the group",
            'rows affected' => $rows
        ], 201);
    }

    public function join(Request $req, Response $res, string $id): Response
    {
        $user_id = $req->getAttribute('user_id');

        $is_user_in_group = $req->getAttribute('is_user_in_group');

        if ($is_user_in_group) {

            return $this->jsonResponse($res, ["Error" => "User is already in a group"], 422);
        }

        $rows = $this->repository->addUserToGroup((int)$id, (int)$user_id);

        return $this->jsonResponse($res, [
            'message' => 'You have been added to the group',
            'rows affected' => $rows,
        ]);
    }

    public function leave(Request $req, Response $res, string $id): Response
    {
        $user_id = $req->getAttribute('user_id');

        $rows = $this->repository->deleteUserFromGroup((int)$id, (int)$user_id);

        return $this->jsonResponse($res, [
            'message' => 'User has left the group',
            'rows affected' => $rows,
        ]);
    }

    public function delete(Request $req, Response $res, string $id): Response
    {
        $rows = $this->repository->delete((int)$id);

        return $this->jsonResponse($res, [
            'message' => 'Group was deleted',
            'rows' => $rows,
        ]);
    }

    public function update(Request $req, Response $res, string $id): Response
    {
        $body = $req->getParsedBody();

        $name = $body['name'];

        $rows = $this->repository->update((int)$id, $name);

        return $this->jsonResponse($res, [
            'message' => 'Group name was updated',
            'rows' => $rows,
        ]);
    }

    private function jsonResponse(Response $res, array $data, int $status = 200): Response
    {
        $res->getBody()->write(json_encode($data));

        return $res->withStatus($status);
    }
}