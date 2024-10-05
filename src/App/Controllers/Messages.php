<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\MessageRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class Messages
{
    public function __construct(private MessageRepository $repository)
    {
    }

    public function getAll(Request $req, Response $res): Response
    {
        $queryParams = $req->getQueryParams();

        $group_id = $queryParams['group_id'];

        $data = $this->repository->getAll((int)$group_id);

        $res->getBody()->write(json_encode($data));

        return $res;
    }

    public function create(Request $req, Response $res): Response
    {
        $body = $req->getParsedBody();

        $user_id = $req->getAttribute('user_id');

        $content = $body['content'];

        $queryParams = $req->getQueryParams();

        $group_id = $queryParams['group_id'];

        $id = $this->repository->create(group_id: $group_id, user_id: $user_id, content: $content);

        $responseBody = json_encode([
            'message' => 'Message sent!',
            'id' => $id
        ]);

        $res->getBody()->write($responseBody);

        return $res->withStatus(201);
    }

    public function delete(Request $req, Response $res, string $id): Response
    {
        $rows = $this->repository->delete((int)$id);

        $responseBody = json_encode([
            'message' => 'Message was deleted',
            'rows' => $rows,]);

        $res->getBody()->write($responseBody);

        return $res;
    }

    public function update(Request $req, Response $res, string $id): Response
    {
        $body = $req->getParsedBody();
        $content = $body['content'];

        $rows = $this->repository->update((int)$id, $content);

        $responseBody = json_encode([
            'message' => 'Message updated',
            'rows' => $rows,
        ]);

        $res->getBody()->write($responseBody);

        return $res;
    }

}