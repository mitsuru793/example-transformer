<?php
declare(strict_types=1);

namespace Php\Domain\HttpCache;

interface HttpRequestCacheRepository
{
    public function store(HttpRequestCache $request): HttpRequestCache;
}
