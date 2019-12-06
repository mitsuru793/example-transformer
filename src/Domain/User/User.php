<?php
declare(strict_types=1);

namespace Php\Domain\User;

use Php\Domain\Book\Book;

final class User
{
    public ?int $id;

    public string $name;

    public function __construct(?int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }
}
