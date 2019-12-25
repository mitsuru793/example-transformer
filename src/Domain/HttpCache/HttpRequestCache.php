<?php
declare(strict_types=1);

namespace Php\Domain\HttpCache;

final class HttpRequestCache
{
    public ?int $id;

    public ?int $responseId;

    public string $method;

    public string $path;

    public array $options;

    public function __construct(?int $id, ?int $responseId, string $method, string $path, array $options)
    {
        $this->id = $id;
        $this->responseId = $responseId;
        $this->method = $method;
        $this->path = $path;
        $this->options = $options;
    }
}
