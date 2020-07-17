<?php
declare(strict_types=1);

namespace Php\Infrastructure\Tables;

abstract class TableBase implements Table
{
    public const TABLE = '';

    public const COLUMNS = [];

    public function __toString(): string
    {
        return static::TABLE;
    }

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
