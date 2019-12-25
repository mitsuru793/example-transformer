<?php
declare(strict_types=1);

namespace Php\Domain\HttpCache;

use Php\Domain\DomainException\DomainRecordNotFoundException;

interface HttpResponseCacheRepository
{
    /**
     * @throws DomainRecordNotFoundException
     */
    public function findByRequest(HttpRequestCache $request): HttpResponseCache;

    public function store(HttpResponseCache $response): HttpResponseCache;
}
