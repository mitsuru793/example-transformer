<?php
declare(strict_types=1);

namespace Php\Domain\UIFacesUser;

final class UIFacesUser
{
    public string $name;

    public string $email;

    public string $position;

    public string $photoUrl;

    public function __construct(string $name, string $email, string $position, string $photoUrl)
    {
        $this->name = $name;
        $this->email = $email;
        $this->position = $position;
        $this->photoUrl = $photoUrl;
    }
}
