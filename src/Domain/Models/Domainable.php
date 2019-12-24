<?php
declare(strict_types=1);

namespace Php\Domain\Models;

interface Domainable
{
    /**
     * @return mixed
     */
    public function toDomain();
}
