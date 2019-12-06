<?php
declare(strict_types=1);

namespace Php\Domain\Post;

use Php\Domain\User\User;

final class Post
{
    public ?int $id;

    public string $title;

    public int $year;

    public User $author;

    public array $viewableUserIds;

    public function __construct(?int $id, string $title, int $year, User $author, array $viewableUserIds = [])
    {
        $this->id = $id;
        $this->title = $title;
        $this->year = $year;
        $this->author = $author;
        $this->viewableUserIds = $viewableUserIds;
    }

    public static function fake(\Faker\Generator $f): self
    {
        static $id = 1;
        return new self(null, $f->sentence, (int)$f->year, User::fake($f));
    }
}
