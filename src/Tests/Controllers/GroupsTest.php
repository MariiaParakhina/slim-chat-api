<?php

declare(strict_types=1);

namespace Tests\Controllers;

use App\Controllers\Groups;
use App\Repositories\GroupRepository;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

class GroupsTest extends TestCase
{
    private Groups $groupsController;
    private GroupRepository $groupRepository;
    private ServerRequestInterface $request;
    private ResponseInterface $response;

    protected function setUp(): void
    {
        $this->groupRepository = $this->createMock(GroupRepository::class);

        $this->groupsController = new Groups($this->groupRepository);

        $this->request = (new ServerRequestFactory())->createServerRequest('GET', '/');

        $this->response = (new ResponseFactory())->createResponse();
    }

    public function testGetAll(): void
    {
        $this->groupRepository->method('getAll')->willReturn([
            ['id' => 1, 'name' => 'group1'],
            ['id' => 2, 'name' => 'group2'],
        ]);

        $response = $this->groupsController->getAll($this->request, $this->response);

        $responseBody = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertJson($responseBody);

        $this->assertStringContainsString('group1', $responseBody);

        $this->assertStringContainsString('group2', $responseBody);
    }

    public function testGetById(): void
    {
        $this->groupRepository->method('getById')->willReturn(['id' => 1, 'name' => 'group1']);

        $response = $this->groupsController->getById($this->request, $this->response, '1');

        $responseBody = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertJson($responseBody);

        $this->assertStringContainsString('group1', $responseBody);
    }

    public function testCreate(): void
    {
        $this->groupRepository->method('create')->willReturn(1);

        $this->groupRepository->method('addUserToGroup')->willReturn(1);

        $request = $this->request->withParsedBody(['name' => 'newgroup'])
            ->withAttribute('user_id', 1);

        $response = $this->groupsController->create($request, $this->response);

        $responseBody = (string) $response->getBody();

        $this->assertEquals(201, $response->getStatusCode());

        $this->assertJson($responseBody);

        $this->assertStringContainsString('Group created', $responseBody);
    }

    public function testJoin(): void
    {
        $this->groupRepository->method('addUserToGroup')->willReturn(1);

        $request = $this->request->withAttribute('user_id', 1)
            ->withAttribute('is_user_in_group', false);

        $response = $this->groupsController->join($request, $this->response, '1');

        $responseBody = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertJson($responseBody);

        $this->assertStringContainsString('You have been added to the group', $responseBody);
    }

    public function testLeave(): void
    {
        $this->groupRepository->method('deleteUserFromGroup')->willReturn(1);

        $request = $this->request->withAttribute('user_id', 1);

        $response = $this->groupsController->leave($request, $this->response, '1');

        $responseBody = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertJson($responseBody);

        $this->assertStringContainsString('User has left the group', $responseBody);
    }

    public function testDelete(): void
    {
        $this->groupRepository->method('delete')->willReturn(1);

        $response = $this->groupsController->delete($this->request, $this->response, '1');

        $responseBody = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertJson($responseBody);

        $this->assertStringContainsString('Group was deleted', $responseBody);
    }

    public function testUpdate(): void
    {
        $this->groupRepository->method('update')->willReturn(1);

        $request = $this->request->withParsedBody(['name' => 'updatedgroup']);

        $response = $this->groupsController->update($request, $this->response, '1');

        $responseBody = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertJson($responseBody);

        $this->assertStringContainsString('Group name was updated', $responseBody);
    }
}