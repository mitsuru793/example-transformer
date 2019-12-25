<?php
declare(strict_types=1);

namespace Php\Domain\HttpCache;

final class HttpRequestHistory
{
    public ?int $id;

    public ?int $requestId;

    public string $method;

    public string $path;

    public array $options;

    public function __construct(?int $id, ?int $requestId, string $method, string $path, array $options)
    {
        $this->id = $id;
        $this->requestId = $requestId;
        $this->method = $method;
        $this->path = $path;
        $this->options = $options;
    }
}
