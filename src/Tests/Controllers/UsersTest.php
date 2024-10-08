<?php

declare(strict_types=1);

namespace Tests\Controllers;

use App\Controllers\Users;
use App\Repositories\UserRepository;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Psr7\Factory\ServerRequestFactory;
use Slim\Psr7\Factory\ResponseFactory;

class UsersTest extends TestCase
{
    private Users $usersController;
    private UserRepository $userRepository;
    private ServerRequestInterface $request;
    private ResponseInterface $response;

    protected function setUp(): void
    {
        $this->userRepository = $this->createMock(UserRepository::class);

        $this->usersController = new Users($this->userRepository);

        $this->request = (new ServerRequestFactory())->createServerRequest('GET', '/');

        $this->response = (new ResponseFactory())->createResponse();
    }

    public function testGetAll(): void
    {
        $this->userRepository->method('getAll')->willReturn([
            ['id' => 1, 'username' => 'user1'],
            ['id' => 2, 'username' => 'user2'],
        ]);

        $response = $this->usersController->getAll($this->request, $this->response);

        $responseBody = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertJson($responseBody);

        $this->assertStringContainsString('user1', $responseBody);

        $this->assertStringContainsString('user2', $responseBody);
    }

    public function testGetById(): void
    {
        $this->userRepository->method('getById')->willReturn(['id' => 1, 'username' => 'user1']);

        $response = $this->usersController->getById($this->request, $this->response, '1');

        $responseBody = (string) $response->getBody();

        $this->assertEquals(200, $response->getStatusCode());

        $this->assertJson($responseBody);

        $this->assertStringContainsString('user1', $responseBody);
    }

    public function testCreate(): void
    {
        $this->userRepository->method('create')->willReturn(1);

        $request = $this->request->withParsedBody(['username' => 'newuser']);

        $response = $this->usersController->create($request, $this->response);

        $responseBody = (string) $response->getBody();

        $this->assertEquals(201, $response->getStatusCode());

        $this->assertJson($responseBody);

        $this->assertStringContainsString('User created', $responseBody);
    }
}