<?php
declare(strict_types=1);

namespace Php;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    private \Php\Library\Fixture\Loader $loader;

    protected function setUp(): void
    {
        parent::setUp();
        $this->loader = new \Php\Library\Fixture\Loader(__DIR__ . '/../fixtures.yml');
    }

    protected function fixtures(): array
    {
        return $this->loader->fixtures();
    }

    protected function fixturesRow(): array
    {
        return $this->loader->fixturesRow();
    }
}
