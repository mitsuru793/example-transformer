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
        return $this->db->find($this->table, $id, [$this, 'toEntity']);
    }

    public function paging(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $rows = $this->db->run(<<<SQL
            SELECT {$this->table->columnsStr()}
            FROM users
            ORDER BY users_id ASC
            LIMIT $perPage OFFSET $offset
            SQL
        );
        return $this->toEntities($rows);
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
            $this->toRows($users),
        );
    }

    public function delete(int $id): void
    {
        $this->db->delete('posts', ['author_id' => $id]);
        $this->db->delete('users', ['id' => $id]);
    }

    public function toEntity(array $row): User
    {
        return new User($row['users_id'], $row['users_name']);
    }

    /**
     * @return User[]
     */
    public function toEntities(array $rows): array
    {
        return array_map(fn ($row) => $this->toEntity($row), $rows);
    }

    public function toRow(User $user): array
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
        ];
    }

    /**
     * @param User[] $users
     */
    public function toRows(array $users): array
    {
        return array_map(fn ($user) => $this->toRow($user), $users);
    }
}
