<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\Eloquent\HttpCache;

use Php\Domain\HttpCache\HttpCache;
use Php\Domain\HttpCache\HttpRequestCache;
use Php\Domain\HttpCache\HttpResponseCache;
use Php\Domain\Models\Domainable;

final class HttpCacheModel extends EloquentBaseModel implements Domainable
{
    /** @var string */
    protected $table = 'http_caches';

    public function toDomain(): HttpCache
    {
        $request = new HttpRequestCache($this->method, $this->path, $this->options);
        $response = new HttpResponseCache($this->responsePhrase, $this->statusCode, $this->headers, $this->protocolVersion, $this->body);
        return new HttpCache($request, $response);
    }
}
