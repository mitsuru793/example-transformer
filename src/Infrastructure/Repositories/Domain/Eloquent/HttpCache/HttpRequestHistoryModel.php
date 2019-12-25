<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\Eloquent\HttpCache;

use Php\Domain\HttpCache\HttpRequestHistory;
use Php\Domain\Models\Domainable;
use Php\Infrastructure\Repositories\Domain\Eloquent\EloquentBaseModel;

/**
 * @property int $id
 * @property int $http_request_cache_id
 *
 * @property HttpRequestCacheModel $requestCache
 */
final class HttpRequestHistoryModel extends EloquentBaseModel implements Domainable
{
    /** @var string */
    protected $table = 'http_request_histories';

    public function requestCache()
    {
        return $this->belongsTo(HttpRequestCacheModel::class, 'http_request_cache_id');
    }

    public function toDomain(): HttpRequestHistory
    {
        $cache = $this->requestCache;
        return new HttpRequestHistory(
            $this->id,
            $this->http_request_cache_id,
            $cache->method,
            $cache->path,
            $cache->options ?? [],
        );
    }
}
