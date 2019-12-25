<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\Eloquent\HttpCache;

use Php\Domain\HttpCache\HttpRequestCache;
use Php\Domain\HttpCache\HttpRequestCacheRepository;
use Php\Library\Util\Arr;

final class EloquentHttpRequestCacheRepository implements HttpRequestCacheRepository
{
    public function store(HttpRequestCache $request): HttpRequestCache
    {
        if (empty($request->id)) {
            $model = new HttpRequestCacheModel();
        } else {
            $model = HttpRequestCacheModel::find($request->id);
        }
        $model->http_response_cache_id = $request->responseId;
        $model->method = $request->method;
        $model->path = $request->path;
        $model->options = Arr::sortDeepByKey($request->options);
        $model->save();

        $request->id = $model->id;
        return $request;
    }

}
