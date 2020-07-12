<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\EasyDB;

use Php\Domain\User\User;
use Php\Domain\User\UserRepository;
use Php\Infrastructure\Tables\UserTable;

final class EasyDBUserRepository implements UserRepository
{
    private ExtendedEasyDB $db;

    private UserTable $table;

    public function __construct(ExtendedEasyDB $db, UserTable $table)
    {
        $this->db = $db;
        $this->table = $table;
    }

    public function find(int $id): ?User
    {
        return $this->db->find($this->table, $id, [$this, 'toUser']);
    }

    public function paging(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $rows = $this->db->run(<<<SQL
            SELECT {$this->columnsStr()}
            FROM users
            ORDER BY users_id ASC
            LIMIT $perPage OFFSET $offset
            SQL
        );
        return array_map(function ($row) {
            return $this->toUser($row);
        }, $rows);
    }

    public function create(User $user): User
    {
        $this->db->insert('users', $this->toRow($user));
        $user->id = (int)$this->db->lastInsertId();
        return $user;
    }

    public function createMany(array $users): void
    {
        $this->db->insertMany(
            $this->table->name(),
            array_map(fn ($user) => $this->toRow($user), $users)
        );
    }

    public function delete(int $id): void
    {
        $this->db->delete('posts', ['author_id' => $id]);
        $this->db->delete('users', ['id' => $id]);
    }

    public function columns(): array
    {
        $columns = ['id', 'name'];
        return array_map(fn ($v) => "users.$v AS users_$v ", $columns);
    }

    public function columnsStr(): string
    {
        return implode(',', $this->columns());
    }

    public function toUser(array $row): User
    {
        return new User($row['users_id'], $row['users_name']);
    }

    public function toRow(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
        ];
    }
}
