<?php
declare(strict_types=1);

namespace Php\Domain\HttpCache;

interface HttpRequestHistoryRepository
{
    public function store(HttpRequestHistory $req): HttpRequestHistory;

    /**
     * @return HttpRequestHistory[]
     */
    public function paging(int $page, int $perPage): array;
}
