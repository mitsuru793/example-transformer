<?php
declare(strict_types=1);

namespace Php\Infrastructure\Tables;

final class UserTable extends TableBase
{
    public const TABLE = 'users';

    public const COLUMNS = [
        'id',
        'name',
        'password',
    ];
}
