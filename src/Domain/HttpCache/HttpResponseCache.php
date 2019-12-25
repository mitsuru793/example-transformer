<?php
declare(strict_types=1);

namespace Php\Domain\HttpCache;

final class HttpResponseCache
{
    public ?int $id;

    public int $requestId;

    public string $responsePhrase;

    public int $statusCode;

    /** @var string[][] */
    public array $headers;

    public string $protocolVersion;

    public string $body;

    public function __construct(?int $id, int $requestId, string $responsePhrase, int $statusCode, $headers, string $protocolVersion, string $body)
    {
        $this->id = $id;
        $this->requestId = $requestId;
        $this->responsePhrase = $responsePhrase;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->protocolVersion = $protocolVersion;
        $this->body = $body;
    }
}
