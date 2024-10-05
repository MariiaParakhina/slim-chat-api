<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Repositories\GroupRepository;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Exception\HttpNotFoundException;
use Slim\Psr7\Response as SlimResponse;
use Slim\Routing\RouteContext;
use Valitron\Validator;

class Groups
{
    public function __construct(private GroupRepository $repository)
    {
    }
    public function getAll(Request $req, Response $res): Response{
        $data = $this->repository->getAll();
        $res->getBody()->write(json_encode($data));
        return $res;
    }
    public function getById(Request $req, Response $res, string $id): Response
    {
        $data = $this->repository->getById((int) $id);

        $body = json_encode($data);

        $res->getBody()->write($body);

        return $res;
    }
    public function create(Request $req, Response $res):Response
    {
        $body = $req->getParsedBody();

        $user_id = $req->getAttribute('user_id');

        $name = $body['name'];

        $id = $this->repository->create($name);


        $rows = $this->repository->addUserToGroup((int)$id, (int)$user_id);

        $responseBody = json_encode([
            'message' => 'Group created',
            'id' => $id,
            'note'=> "you have been added to the group",
            'rows affected' => $rows,

        ]);

        $res->getBody()->write($responseBody);

        return $res->withStatus(201);
    }

    public function join(Request $req, Response $res, string $id): Response{
        $user_id = $req->getAttribute('user_id');

        $is_user_in_group = $req->getAttribute('is_user_in_group');

        if($is_user_in_group){

            $res->getBody()->write(json_encode(["Error"=>"User is already in a group"]));
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

    public function leave(Request $req, Response $res, string $id): Response{
        $user_id = $req->getAttribute('user_id');

        $is_user_in_group = $req->getAttribute('is_user_in_group');

        if(!$is_user_in_group){

            $res->getBody()->write(json_encode(["Error"=>"User is not in this group"]));
            return $res->withStatus(422);
        }

        $rows = $this->repository->deleteUserFromGroup((int)$id, (int)$user_id);

        $responseBody = json_encode([
            'message' => 'user have left the group',
            'rows affected' => $rows,

        ]);

        $res->getBody()->write($responseBody);

        return $res;
    }

}