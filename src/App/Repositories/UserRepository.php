<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use PDO;


class UserRepository
{

    public function __construct(private Database $database)
    {

    }

    public function getToken(string $token): array|bool
    {

        $sql = 'SELECT * FROM users WHERE   token = :token';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':token', $token, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    public function getAll(): array
    {

        $pdo = $this->database->getConnection();

        $stmt = $pdo->query('SELECT * from users');

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }

    public function getById(int $id): array|bool
    {
        $sql = 'SELECT * FROM users WHERE id = :id';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByUsername(string $username): array|bool
    {
        $sql = 'SELECT * FROM users WHERE username= :username';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':username', $username, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(string $username, string $token): int
    {
        $sql = 'INSERT INTO users (username, token ) VALUES (:username, :token)';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':username', $username, PDO::PARAM_STR);

        $stmt->bindValue(':token', $token, PDO::PARAM_STR);

        $stmt->execute();

        return (int)$pdo->lastInsertId();
    }

    public function update(int $id, string $username): int
    {
        $sql = 'UPDATE users SET username = :username WHERE id = :id';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->bindValue(':username', $username, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public function delete(int $id): int
    {
        $sql = 'DELETE FROM users WHERE id = :id';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }


}