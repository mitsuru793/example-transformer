<?php
declare(strict_types=1);

namespace Php\Presenter;

final class Button
{
    public string $label;

    public string $httpMethod;

    public string $url;

    public function __construct(string $label, string $httpMethod, string $url)
    {
        $this->label = $label;
        $this->httpMethod = $httpMethod;
        $this->url = $url;
    }
}
