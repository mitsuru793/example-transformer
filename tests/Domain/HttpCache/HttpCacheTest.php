<?php
declare(strict_types=1);

namespace Helper\Domain\HttpCache;

use Php\Domain\DomainException\DomainRecordNotFoundException;
use Php\Domain\HttpCache\HttpRequestCache;
use Php\Domain\HttpCache\HttpRequestCacheRepository;
use Php\Domain\HttpCache\HttpResponseCacheRepository;
use Php\Helper\TestBase;
use Php\Infrastructure\Repositories\Domain\Eloquent\HttpCache\EloquentHttpRequestCacheRepository;
use Php\Infrastructure\Repositories\Domain\Eloquent\HttpCache\EloquentHttpResponseCacheRepository;
use Psr\Http\Message\ResponseInterface;

final class HttpCacheTest extends TestBase
{
    private HttpRequestCacheRepository $reqCacheRepo;

    private HttpResponseCacheRepository $resRepo;

    public function setUp(): void
    {
        parent::setUp();
        $this->reqCacheRepo = new EloquentHttpRequestCacheRepository();
        $this->resRepo = new EloquentHttpResponseCacheRepository();
    }

    public function testStoreRequestAndResponse()
    {
        $request = new \Php\Domain\HttpCache\HttpRequestCache(
            null,
            null,
            'GET',
            'http://example.com/',
            [],
        );

        try {
            $this->resRepo->findByRequest($request);
            $this->fail('Not reset database and request cache exists.');
            return;
        } catch (DomainRecordNotFoundException $e) {
        }

        $client = new \GuzzleHttp\Client();
        $response = $client->request($request->method, $request->path, $request->options);

        $this->createCache($request, $response);

        try {
            $storedResCache = $this->resRepo->findByRequest($request);
        } catch (DomainRecordNotFoundException $e) {
            $this->fail($e->getMessage());
        }

        $this->assertSame((string)$response->getBody(), $storedResCache->body);
    }

    private function createCache(HttpRequestCache $req, ResponseInterface $res)
    {
        $reqCache = $this->reqCacheRepo->store($req);

        $resCache = new \Php\Domain\HttpCache\HttpResponseCache(
            null,
            $req->id,
            $res->getReasonPhrase(),
            $res->getStatusCode(),
            $res->getHeaders(),
            $res->getProtocolVersion(),
            (string)$res->getBody(),
        );

        $resCache = $this->resRepo->store($resCache);

        $reqCache->responseId = $resCache->id;
        $this->reqCacheRepo->store($reqCache);
    }
}
