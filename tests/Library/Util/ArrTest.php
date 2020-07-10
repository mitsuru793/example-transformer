<?php
declare(strict_types=1);

namespace Php\Library\Util;

use Php\Helper\TestBase;

final class ArrTest extends TestBase
{
    public function testSortDeepByKey()
    {
        $v = fn (string $s) => (time() . ":$s");
        $input = [
            'd' => [
                'f' => $v('f'),
                'e' => $v('e'),
                'g' => [
                    'j' => $v('j'),
                    'h' => $v('h'),
                    'i' => $v('i'),
                ],
            ],
            'a' => $v('a'),
            'c' => $v('c'),
            'b' => $v('b'),
        ];

        $sorted = Arr::sortDeepByKey($input);
        $expected = [
            'a' => $input['a'],
            'b' => $input['b'],
            'c' => $input['c'],
            'd' => [
                'e' => $input['d']['e'],
                'f' => $input['d']['f'],
                'g' => [
                    'h' => $input['d']['g']['h'],
                    'i' => $input['d']['g']['i'],
                    'j' => $input['d']['g']['j'],
                ],
            ],
        ];
        $this->assertSame($expected, $sorted);
    }
}
