<?php
declare(strict_types=1);

namespace Php\Infrastructure\Tables;

final class TagTable extends TableBase
{
    public const TABLE = 'tags';

    public const COLUMNS = [
        'id',
        'name',
    ];
}
