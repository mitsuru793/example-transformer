<?php
declare(strict_types=1);

namespace Php\Infrastructure\Tables;

final class TwitterAccessTokenTable extends TableBase
{
    public const TABLE = 'twitter_oauth_access_tokens';

    public const COLUMNS = [
        'id',
        'twitter_user_id',
        'screen_name',
        'token',
        'secret',
    ];
}