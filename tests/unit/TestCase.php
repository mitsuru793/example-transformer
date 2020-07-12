<?php
declare(strict_types=1);

namespace Php;

use Nelmio\Alice\ObjectSet;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    protected function fixtures(): array
    {
        static $data;
        if ($data) {
            return $data;
        }

        $loader = new \Nelmio\Alice\Loader\NativeLoader();
        $data = $loader->loadFile(__DIR__ . '/../fixtures.yml')->getObjects();
        return $data;
    }
}
