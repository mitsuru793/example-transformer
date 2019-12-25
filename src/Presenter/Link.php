<?php
declare(strict_types=1);

namespace Php\Presenter;

final class Link
{
    public string $label;

    public string $url;

    public function __construct(string $label, string $url)
    {
        $this->label = $label;
        $this->url = $url;
    }
}
