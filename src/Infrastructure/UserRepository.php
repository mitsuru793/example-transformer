<?php
declare(strict_types=1);

namespace Php\Infrastructure;

use Php\Domain\User\User;

final class UserRepository
{
    /** @var Database */
    private $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function find(int $id): User
    {
        $row = $this->db->row(
            "SELECT {$this->columnsStr()} FROM users WHERE users.id = ?",
            $id,
            );
        return new User((int)$row['users_id'], $row['users_name']);
    }

    public function create(User $user): User
    {
        $this->db->insert('users', [
            'name' => $user->name,
        ]);
        $user->id = (int)$this->db->lastInsertId();
        return $user;
    }

    public function columns(): array
    {
        $columns = ['id', 'name'];
        return array_map(fn($v) => "users.$v AS users_$v ", $columns);
    }

    public function columnsStr(): string
    {
        return implode(',', $this->columns());
    }

    public function toUser(array $row): User
    {
        logs($row);
        return new User($row['users_id'], $row['users_name']);
    }
}
