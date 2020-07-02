<?php
declare(strict_types=1);

namespace Php\Library\Util;

final class Origin
{
    public static function api(): string
    {
        return sprintf('%s://%s:%s', getenv('SCHEME'), getenv('MYAPP_API_DOMAIN'), getenv('MYAPP_API_PORT'));
    }

    public static function web(): string
    {
        return sprintf('%s://%s:%s', getenv('SCHEME'), getenv('MYAPP_WEB_DOMAIN'), getenv('MYAPP_WEB_PORT'));
    }
}