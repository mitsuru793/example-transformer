<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\Eloquent\HttpCache;

use Php\Domain\HttpCache\HttpRequestHistory;
use Php\Domain\HttpCache\HttpRequestHistoryRepository;
use Php\Domain\Models\Domainable;

final class EloquentHttpRequestHistoryRepository implements HttpRequestHistoryRepository
{
    public function store(HttpRequestHistory $req): HttpRequestHistory
    {
        $model = new HttpRequestHistoryModel();
        $model->http_request_cache_id = $req->requestId;
        $model->save();

        $req->id = $model->id;
        return $req;
    }

    public function paging(int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        return HttpRequestHistoryModel::offset($offset)->limit($perPage)
            ->get()->map(fn(Domainable $m) => $m->toDomain())->toArray();
    }
}
