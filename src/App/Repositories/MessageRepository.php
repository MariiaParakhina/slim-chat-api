<?php

declare(strict_types=1);

namespace App\Repositories;
use App\Database;
use PDO;

class MessageRepository
{

    public function __construct(private Database $database)
    {

    }
    public function getAll(int $group_id): array{
        $sql = 'SELECT messages.id, messages.content, messages.created_at, users.username
                FROM messages
                JOIN users ON messages.user_id = users.id
                WHERE messages.group_id = :group_id
                ORDER BY messages.created_at ASC';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':group_id', $group_id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }


    public function create(int $group_id, int $user_id, string $content): string
    {
        $sql = 'INSERT INTO messages (group_id, user_id, content) VALUES (:group_id, :user_id, :content)';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':group_id', $group_id, PDO::PARAM_INT);

        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        $stmt->bindValue(':content', $content, PDO::PARAM_STR);

        $stmt->execute();

        return $pdo->lastInsertId();
    }



    public function update(int $id, string $content): int{
        $sql = 'UPDATE messages SET content = :content WHERE id = :id';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->bindValue(':content', $content, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public function delete(int $id): int {
        $sql = 'DELETE FROM messages WHERE id = :id';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }


}