<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Database;
use PDO;

class GroupRepository
{

    public function __construct(private Database $database)
    {

    }

    public function getAll(): array
    {
        $sql = 'SELECT g.id, g.name, g.created_at, COUNT(gm.user_id) as member_count
                FROM groups g
                LEFT JOIN group_memberships gm ON g.id = gm.group_id
                GROUP BY g.id, g.name, g.created_at ';

        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById(int $id): array
    {
        $sql = 'SELECT g.id, g.name, g.created_at, u.username
                FROM groups g
                LEFT JOIN group_memberships gm ON g.id = gm.group_id
                LEFT JOIN users u ON gm.user_id = u.id
                WHERE g.id = :id';

        $pdo = $this->database->getConnection();
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $group = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            if (empty($group)) {
                $group = [
                    'id' => $row['id'],
                    'name' => $row['name'],
                    'created_at' => $row['created_at'],
                    'members' => []
                ];
            }
            if ($row['username']) {
                $group['members'][] = $row['username'];
            }
        }

        return $group;

    }

    public function getByName(string $name): array|bool
    {
        $sql = 'SELECT * FROM groups WHERE name= :name';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':name', $name, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create(string $name): int
    {
        $sql = 'INSERT INTO groups (name) VALUES (:name)';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':name', $name, PDO::PARAM_STR);

        $stmt->execute();

        return (int)$pdo->lastInsertId();
    }

    public function isUserInGroup(int $group_id, int $user_id): int
    {
        $sql = 'SELECT COUNT(*) FROM group_memberships WHERE group_id = :group_id AND user_id = :user_id';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':group_id', $group_id, PDO::PARAM_INT);

        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        $stmt->execute();

        $count = $stmt->fetchColumn();

        return $count !== false ? (int)$count : 0;

    }

    public function addUserToGroup(int $group_id, int $user_id): int
    {

        $sql = 'INSERT INTO group_memberships (group_id, user_id) VALUES (:group_id, :user_id)';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':group_id', $group_id, PDO::PARAM_INT);

        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();

    }

    public function deleteUserFromGroup(int $group_id, int $user_id): int
    {
        $sql = 'DELETE FROM group_memberships WHERE group_id = :group_id AND user_id = :user_id';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':group_id', $group_id, PDO::PARAM_INT);

        $stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public function update(int $id, string $name): int
    {
        $sql = 'UPDATE groups SET name = :name WHERE id = :id';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        $stmt->bindValue(':name', $name, PDO::PARAM_STR);

        $stmt->execute();

        return $stmt->rowCount();
    }

    public function delete(int $group_id): int
    {
        $sql = 'DELETE FROM groups WHERE id = :group_id';

        $pdo = $this->database->getConnection();

        $stmt = $pdo->prepare($sql);

        $stmt->bindValue(':group_id', $group_id, PDO::PARAM_INT);

        $stmt->execute();

        return $stmt->rowCount();
    }


}