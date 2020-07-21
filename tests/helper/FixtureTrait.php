<?php
declare(strict_types=1);

namespace Helper;

use Php\Library\Fixture\AliceFixture;

trait FixtureTrait
{
    protected function fixtures(): array
    {
        return $this->loader()->fixtures();
    }

    protected function f(): TypedFixture
    {
        $f = new AliceFixture($this->loader()->fixtures());
        return new TypedFixture($f);
    }

    protected function fixturesRow(): array
    {
        return $this->loader()->fixturesRow();
    }

    abstract public function loader(): \Php\Library\Fixture\Loader;
}