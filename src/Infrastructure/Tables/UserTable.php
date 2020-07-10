<?php
declare(strict_types=1);

namespace Php\Infrastructure\Tables;

final class UserTable extends Table
{
    public const TABLE = 'users';

    public const COLUMNS = [
        'id',
        'name',
    ];
}