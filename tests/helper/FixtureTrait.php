<?php
declare(strict_types=1);

namespace Helper;

trait FixtureTrait
{
    protected function fixtures(): array
    {
        return $this->loader()->fixtures();
    }

    protected function fixturesRow(): array
    {
        return $this->loader()->fixturesRow();
    }

    abstract public function loader(): \Php\Library\Fixture\Loader;
}