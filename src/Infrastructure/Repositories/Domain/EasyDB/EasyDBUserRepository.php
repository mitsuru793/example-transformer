<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\EasyDB;

use Php\Domain\User\User;
use Php\Domain\User\UserRepository;

final class EasyDBUserRepository implements UserRepository
{
    /** @var ExtendedEasyDB */
    private $db;

    public function __construct(ExtendedEasyDB $db)
    {
        $this->db = $db;
    }

    public function find(int $id): ?User
    {
        $row = $this->db->row(
            "SELECT {$this->columnsStr()} FROM users WHERE users.id = ?",
            $id,
            );
        if (!$row) {
            return null;
        }
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
