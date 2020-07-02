<?php
declare(strict_types=1);

namespace Php\Library\Util;

final class Path
{
    public static function root(): string
    {
        return __DIR__ . '/../../..';
    }

    public static function webRoot(): string
    {
        return self::root() . '/public';
    }
}