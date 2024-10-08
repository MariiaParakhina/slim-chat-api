<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\MessageRepository;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use WebSocket\Client;

class Messages
{
    public function __construct(private MessageRepository $repository)
    {
    }

    public function getAll(Request $req, Response $res): Response
    {
        $group_id = (int)$req->getQueryParams()['group_id'];

        $data = $this->repository->getAll($group_id);

        return $this->jsonResponse($res, $data);
    }

    public function create(Request $req, Response $res): Response
    {
        $body = $req->getParsedBody();

        $user_id = $req->getAttribute('user_id');

        $content = $body['content'];

        $group_id = (int)$req->getQueryParams()['group_id'];

        $id = $this->repository->create(group_id: $group_id, user_id: $user_id, content: $content);

        $this->sendWebSocketMessage([
            "user_id" => $user_id,
            'group_id' => $group_id,
            'message_id' => $id,
            'content' => $content,
        ]);

        return $this->jsonResponse($res, [
            'message' => 'Message sent!',
            'id' => $id
        ], 201);
    }

    public function delete(Request $req, Response $res, string $id): Response
    {
        $rows = $this->repository->delete((int)$id);

        return $this->jsonResponse($res, [
            'message' => 'Message was deleted',
            'rows' => $rows,
        ]);
    }

    public function update(Request $req, Response $res, string $id): Response
    {
        $content = $req->getParsedBody()['content'];

        $rows = $this->repository->update((int)$id, $content);

        return $this->jsonResponse($res, [
            'message' => 'Message updated',
            'rows' => $rows,
        ]);
    }

    private function jsonResponse(Response $res, array $data, int $status = 200): Response
    {
        $res->getBody()->write(json_encode($data));

        return $res->withStatus($status);
    }

    private function sendWebSocketMessage(array $message): void
    {
        $client = new Client("ws://localhost:8083/chat");

        $client->send(json_encode($message));
    }
}