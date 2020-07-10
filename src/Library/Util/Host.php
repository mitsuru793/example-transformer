<?php
declare(strict_types=1);

namespace Php\Library\Util;

final class Host
{
    public static function api(): string
    {
        return sprintf('%s', getenv('MYAPP_API_DOMAIN'));
    }
}
