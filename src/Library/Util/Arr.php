<?php
declare(strict_types=1);

namespace Php\Library\Util;

final class Arr
{
    public static function sortDeepByKey(array $array): array
    {
        $sorted = [];
        foreach ($array as $key => $val) {
            if (is_array($val)) {
                $val = self::sortDeepByKey($val);
            }
            $sorted[$key] = $val;
        }
        array_multisort($sorted);
        return $sorted;
    }
}
