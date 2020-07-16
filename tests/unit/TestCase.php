<?php
declare(strict_types=1);

namespace Php;

use Helper\FixtureTrait;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    use FixtureTrait;

    private \Php\Library\Fixture\Loader $loader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loader = new \Php\Library\Fixture\Loader(__DIR__ . '/../fixtures.yml');
    }

    public function loader(): \Php\Library\Fixture\Loader
    {
        return $this->loader;
    }
}
