<?php
declare(strict_types=1);

namespace Php\Domain\User;

final class User
{
    public ?int $id;

    public string $name;

    public string $password;

    public function __construct(?int $id, string $name, string $password)
    {
        $this->id = $id;
        $this->name = $name;
        $this->password = $password;
    }
}
