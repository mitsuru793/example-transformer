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
    public function get(string $key = null)
    {
        if (is_null($key)) {
            return $this->fixtures;
        }

        $regexp = '/\{(\d+)\.{2}(\d+)}/';
        preg_match($regexp, $key, $match);
        if (!$match) {
            return $this->fixtures[$key];
        }

        [, $start, $end] = $match;

        $keys = array_map(fn ($i) => preg_replace($regexp, $i, $key), range($start, $end));
        $collected = [];
        foreach ($keys as $k) {
            $collected[$k] = $this->fixtures[$k];
        }
        return $collected;
    }
}
