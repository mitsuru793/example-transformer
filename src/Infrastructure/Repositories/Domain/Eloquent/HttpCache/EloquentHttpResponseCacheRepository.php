<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\Eloquent\HttpCache;

use Php\Domain\DomainException\DomainRecordNotFoundException;
use Php\Domain\HttpCache\HttpRequestCache;
use Php\Domain\HttpCache\HttpResponseCache;
use Php\Domain\HttpCache\HttpResponseCacheRepository;
use Php\Library\Util\Arr;

final class EloquentHttpResponseCacheRepository implements HttpResponseCacheRepository
{
    public function findByRequest(HttpRequestCache $request): HttpResponseCache
    {
        if (empty($request->options)) {
            $options = null;
        } else {
            $options = json_encode(Arr::sortDeepByKey($request->options));
        }
        $where = [
            'method' => $request->method,
            'path' => $request->path,
            'options' => $options,
        ];
        /** @var HttpRequestCacheModel $reqModel */
        $reqModel = HttpRequestCacheModel::where($where)->first();
        if (!$reqModel) {
            $msg = sprintf('Not found %s by request(%s)', HttpRequestCacheModel::class, json_encode($where));
            throw new DomainRecordNotFoundException($msg);
        }
        if (!$reqModel->httpResponseCache) {
            $msg = sprintf('Not found %s by request(%s)', HttpResponseCache::class, json_encode($where));
            throw new DomainRecordNotFoundException($msg);
        }

        return $reqModel->httpResponseCache->toDomain();
    }

    public function store(HttpResponseCache $response): HttpResponseCache
    {
        $model = new HttpResponseCacheModel();
        $model->id = $response->id;
        $model->http_request_cache_id = $response->requestId;
        $model->response_phrase = $response->responsePhrase;
        $model->status_code = $response->statusCode;
        $model->headers = $response->headers;
        $model->protocol_version = $response->protocolVersion;
        $model->body = $response->body;
        $model->save();

        $response->id = $model->id;
        return $response;
    }
}
