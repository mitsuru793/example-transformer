<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\EasyDB;

use ParagonIE\EasyDB\EasyDB;
use Php\Infrastructure\Tables\Table;

final class ExtendedEasyDB extends EasyDB
{
    public function runSqlFile(string $path): void
    {
        $lines = explode(';', file_get_contents($path));
        foreach ($lines as $sql) {
            $sql = trim($sql);
            if (empty($sql)) {
                continue;
            }
            $this->run($sql);
        }
    }

    /**
     * @return mixed|array|null
     */
    public function find(Table $table, int $id, callable $transformer = null)
    {
        $columns = implode(', ', $table->columns());
        $row = $this->row(
            "SELECT {$columns} FROM {$table->name()} WHERE {$table->name()}.id = ?",
            $id,
        );
        if (!$row) {
            return null;
        }
        if (is_null($transformer)) {
            return $row;
        }
        return $transformer($row);
    }

    /**
     * @return mixed|array|null
     */
    public function findBy(Table $table, string $column, int $id, callable $transformer = null)
    {
        $columns = implode(', ', $table->columns());
        $row = $this->row(
            "SELECT {$columns} FROM {$table->name()} WHERE {$table->name()}.{$column} = ?",
            $id,
        );
        if (!$row) {
            return null;
        }
        if (is_null($transformer)) {
            return $row;
        }
        return $transformer($row);
    }
}
