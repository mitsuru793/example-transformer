<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\Eloquent\HttpCache;

use Php\Domain\HttpCache\HttpRequestCache;
use Php\Domain\HttpCache\HttpResponseCache;
use Php\Domain\Models\Domainable;
use Php\Infrastructure\Repositories\Domain\Eloquent\EloquentBaseModel;

/**
 * @property int $id
 * @property int $http_response_cache_id
 *
 * @property string $method
 * @property string $path
 * @property array $options
 *
 * @property HttpResponseCacheModel $httpResponseCache
 */
final class HttpRequestCacheModel extends EloquentBaseModel implements Domainable
{
    /** @var string */
    protected $table = 'http_request_caches';

    protected $casts = [
        'options' => 'json',
    ];

    public function setOptionsAttribute($value)
    {
        $this->options = json_encode($value);
    }

    public function httpResponseCache()
    {
        return $this->hasOne(HttpResponseCacheModel::class, 'http_request_cache_id');
    }

    public function toDomain(): HttpRequestCache
    {
        return new HttpRequestCache($this->id, $this->http_response_cache_id, $this->method, $this->path, $this->options);
    }
}
