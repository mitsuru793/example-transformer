<?php
declare(strict_types=1);

namespace Php\Infrastructure\Tables;

final class PostTable extends TableBase
{
    public const TABLE = 'posts';

    public const COLUMNS = [
        'id',
        'author_id',
        'title',
        'content',
        'viewable_user_ids',
        'year',
    ];
}
