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

    public function find(Table $table, int $id): ?array
    {
        $columns = implode(', ', $table->columns());
        $row = $this->row(
            "SELECT {$columns} FROM {$table->name()} WHERE {$table->name()}.id = ?",
            $id,
        );
        return $row ? $row : null;
    }

    public function insertModel($models): void
    {
        if (!is_array($models)) {
            $models = [$models];
        }

        $table = $this->tableName($models[0]);

        foreach ($models as $model) {
            $this->insert($table);
        }
    }

    private function tableName($model): string
    {
        $class = strtolower(get_class($model)) . 's';
        return substr(strrchr($class, "\\"), 1);
    }

    private function foreignKey($model): string
    {
        $fullClass = strtolower(get_class($model));
        $class = substr(strrchr($fullClass, "\\"), 1);
        return $class . '_id';
    }
}
