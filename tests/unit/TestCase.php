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
            $new = array_map(fn ($v) => clone $v, $data);
            return $new;
        }

        $loader = new \Nelmio\Alice\Loader\NativeLoader();
        $data = $loader->loadFile(__DIR__ . '/../fixtures.yml')->getObjects();
        return $data;
    }

    protected function fixturesRow(): array
    {
        static $data;
        if ($data) {
            return $data;
        }

        $data = json_decode(json_encode($this->fixtures()), true);
        // Rename key of only each items, but not fixture name.
        $data = array_map(fn ($fixture) => $this->keyToSnakeCaseRecursive($fixture), $data);
        return $data;
    }

    private function keyToSnakeCaseRecursive(array $array): array
    {
        return array_map(
            function ($item) {
                if (is_array($item)) {
                    $item = $this->keyToSnakeCaseRecursive($item);
                }

                return $item;
            },
            $this->keyToSnakeCase($array)
        );
    }

    private function keyToSnakeCase(array $array): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            $key = strtolower(preg_replace('~(?<=\\w)([A-Z])~', '_$1', $key));
            $result[$key] = $value;
        }
        return $result;
    }
}
