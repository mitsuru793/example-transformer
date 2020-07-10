<?php
declare(strict_types=1);

namespace Php\Infrastructure\Tables;

interface Table
{
    public function name(): string;

    /**
     * @return string[]
     */
    public function columns(): array;
}
