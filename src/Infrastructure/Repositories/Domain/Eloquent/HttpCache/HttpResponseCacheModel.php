<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\Eloquent\HttpCache;

use Php\Domain\HttpCache\HttpResponseCache;
use Php\Domain\Models\Domainable;
use Php\Infrastructure\Repositories\Domain\Eloquent\EloquentBaseModel;

/**
 * @property int $id
 * @property int $http_request_cache_id
 *
 * @property string $response_phrase
 * @property int $status_code
 * @property array $headers
 * @property string $protocol_version
 * @property string $body
 */
final class HttpResponseCacheModel extends EloquentBaseModel implements Domainable
{
    /** @var string */
    protected $table = 'http_response_caches';

    protected $casts = [
        'headers' => 'array',
    ];

    public function httpRequestCache()
    {
        return $this->hasOne(HttpRequestCacheModel::class, 'http_response_cache_id');
    }

    public function toDomain(): HttpResponseCache
    {
        return new HttpResponseCache(
            $this->id,
            $this->http_request_cache_id,
            $this->response_phrase,
            $this->status_code,
            $this->headers,
            $this->protocol_version,
            $this->body
        );
    }
}
