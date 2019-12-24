<?php
declare(strict_types=1);

namespace Php\Domain\UIFacesUser;

final class UIFacesUser
{
    public ?int $id;

    public string $name;

    public string $email;

    public string $position;

    public string $photoUrl;

    public function __construct(?int $id, string $name, string $email, string $position, string $photoUrl)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->position = $position;
        $this->photoUrl = $photoUrl;
    }
}
