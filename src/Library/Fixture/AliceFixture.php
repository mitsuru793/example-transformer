<?php
declare(strict_types=1);

namespace Php\Library\Fixture;

final class AliceFixture
{
    private array $fixtures;

    public function __construct(array $data)
    {
        $this->fixtures = $data;
    }

    /**
     * @return mixed
     */
    public function get(string $key = null, bool $reIndex = false)
    {
        if (is_null($key)) {
            if (!$reIndex) {
                return $this->fixtures;
            }
            return array_values($this->fixtures);
        }

        $regexp = '/^.+\{(\d+)\.{2}(\d+)}/';
        preg_match($regexp, $key, $match);
        if (!$match) {
            if (strpos($key, '.') !== false) {
                [$k, $prop] = explode('.', $key);
                return $this->getValue($this->fixtures[$k], $prop);
            }
            return $this->fixtures[$key];
        }

        [$root, $start, $end] = $match;

        $prop = substr($key, strlen($root));
        if (!empty($prop)) {
            if (strpos($prop, '.') !== 0) {
                $err = sprintf('Invalid position of dot of key: %s', $key);
                throw new \InvalidArgumentException($err);
            }
            $prop = substr($prop, 1);
        }

        $collected = [];

        if (empty($prop)) {
            $regexp = '/\{(\d+)\.{2}(\d+)}/';
            $keys = array_map(fn ($i) => preg_replace($regexp, $i, $key), range($start, $end));
            foreach ($keys as $k) {
                if ($reIndex) {
                    $collected[] = $this->fixtures[$k];
                } else {
                    $collected[$k] = $this->fixtures[$k];
                }
            }
        } else {
            $regexp = '/\{(\d+)\.{2}(\d+)}.+/';
            $keys = array_map(fn ($i) => preg_replace($regexp, $i, $key), range($start, $end));
            $collected = array_map(function ($k) use ($prop) {
                return $this->getValue($this->fixtures[$k], $prop);
            }, $keys);
        }
        return $collected;
    }

    /**
     * @param object|array $value
     * @return mixed
     */
    private function getValue($value, string $prop)
    {
        if (is_object($value)) {
            return $value->{$prop};
        }
        assert(is_array($value));
        return $value[$prop];
    }
}
