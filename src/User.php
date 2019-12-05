<?php
declare(strict_types=1);

namespace Php;

final class User
{
    public int $id;
    public string $name;

    public function __construct(int $id, string $name)
    {
        $this->id = $id;
        $this->name = $name;
    }

    public static function fake(\Faker\Generator $f): self
    {
        static $id = 1;
        return new self($id++, $f->name);
    }
}
