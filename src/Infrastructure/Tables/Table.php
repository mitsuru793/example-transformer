<?php
declare(strict_types=1);

namespace Php\Infrastructure\Tables;

abstract class Table
{
    public const TABLE = '';

    public const COLUMNS = [];

    public function name(): string
    {
        return static::TABLE;
    }

    public function columns(): array
    {
        return array_map(fn ($column) => sprintf("%s.$column AS %s_$column ", static::TABLE, static::TABLE), static::COLUMNS);
    }

    public function columnsStr(): string
    {
        return implode(',', $this->columns());
    }
}