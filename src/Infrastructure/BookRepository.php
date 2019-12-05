<?php
declare(strict_types=1);

namespace Php\Infrastructure;

use Php\Domain\Book\Book;

final class BookRepository
{
    private Database $db;

    private UserRepository $userRepo;

    public function __construct(Database $db, UserRepository $userRepo)
    {
        $this->db = $db;
        $this->userRepo = $userRepo;
    }

    public function create(Book $book)
    {
        $this->db->insert('books', [
            'title' => $book->title,
            'year' => $book->year,
            'author_id' => $book->author->id,
            'viewable_user_ids' => json_encode($book->viewableUserIds),
        ]);
    }

    public function paging(int $page, int $count): array
    {
        $offset = ($page -1 ) * $count;
        $rows = $this->db->run(<<<SQL
            SELECT {$this->columnsStr()}, {$this->userRepo->columnsStr()}
            FROM books
            INNER JOIN users ON users.id = books.author_id
            ORDER BY books_id ASC
            LIMIT $count OFFSET $offset
            SQL
        );
        return array_map(function ($row) {
            return $this->toBook($row);
        }, $rows);
    }

    public function columns(): array
    {
        $columns = ['id', 'title', 'year', 'author_id', 'viewable_user_ids'];
        return array_map(fn($v) => "books.$v AS books_$v", $columns);
    }

    public function columnsStr(): string
    {
        return implode(',', $this->columns());
    }

    public function toBook(array $row): Book
    {
        $author = $this->userRepo->toUser($row);
        return new Book((int)$row['books_id'], $row['books_title'], $row['books_year'], $author, json_decode($row['books_viewable_user_ids']));
    }
}
