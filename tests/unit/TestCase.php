<?php
declare(strict_types=1);

namespace Php;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    public function fixtures()
    {
        static $objectSet;
        if (!$objectSet) {
            return $objectSet;
        }

        $loader = new \Nelmio\Alice\Loader\NativeLoader();
        $objectSet = $loader->loadFile(__DIR__ . '/../fixtures.yml');
        return $objectSet;
    }
}
