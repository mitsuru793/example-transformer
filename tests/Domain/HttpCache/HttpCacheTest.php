<?php
declare(strict_types=1);

namespace Helper\Domain\HttpCache;

use Php\Domain\DomainException\DomainRecordNotFoundException;
use Php\Domain\HttpCache\HttpRequestCache;
use Php\Domain\HttpCache\HttpRequestCacheRepository;
use Php\Domain\HttpCache\HttpRequestHistory;
use Php\Domain\HttpCache\HttpRequestHistoryRepository;
use Php\Domain\HttpCache\HttpResponseCacheRepository;
use Php\Helper\TestBase;
use Php\Infrastructure\Repositories\Domain\Eloquent\HttpCache\EloquentHttpRequestCacheRepository;
use Php\Infrastructure\Repositories\Domain\Eloquent\HttpCache\EloquentHttpRequestHistoryRepository;
use Php\Infrastructure\Repositories\Domain\Eloquent\HttpCache\EloquentHttpResponseCacheRepository;
use Psr\Http\Message\ResponseInterface;

final class HttpCacheTest extends TestBase
{
    private HttpRequestCacheRepository $reqCacheRepo;

    private HttpResponseCacheRepository $resRepo;

    private HttpRequestHistoryRepository $historyRepo;

    public function setUp(): void
    {
        parent::setUp();
        $this->reqCacheRepo = new EloquentHttpRequestCacheRepository();
        $this->resRepo = new EloquentHttpResponseCacheRepository();
        $this->historyRepo = new EloquentHttpRequestHistoryRepository();
    }

    public function testStoreRequestAndResponse()
    {
        $request = $this->createRequest('GET', 'http://example.com/', [] );

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

    public function testHistory()
    {
        $base = 'http://example.com';
        $req = $this->createRequest('GET', "$base", []);
        $reqCache = $this->reqCacheRepo->store($req);
        $this->historyRepo->store($reqCache->toHistory());

        $histories = $this->historyRepo->paging(1, 1);
        $this->assertCount(1, $histories);
        $this->assertInstanceOf(HttpRequestHistory::class, $histories[0]);
        $this->assertSame($reqCache->id, $histories[0]->requestId);
    }

    private function createRequest(string $method, string $path, array $options): HttpRequestCache
    {
        return new HttpRequestCache(
            null,
            null,
            $method,
            $path,
            $options,
        );
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
