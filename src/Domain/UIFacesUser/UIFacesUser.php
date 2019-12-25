<?php
declare(strict_types=1);

namespace Php\Domain\UIFacesUser;

final class UIFacesUser
{
    private const PHOTO_FILE_REGEXP = [
        '@^https?://@' => '',
        '@/@' => '_',
    ];

    public ?int $id;

    public string $name;

    public string $email;

    public string $position;

    public string $photoUrl;

    public string $photoFile;

    public function __construct(?int $id, string $name, string $email, string $position, string $photoUrl, string $photoFile)
    {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->position = $position;
        $this->photoUrl = $photoUrl;
        $this->photoFile = $photoFile;
    }

    public static function createPhotoFile(string $photoUrl)
    {
        return preg_replace(
            array_keys(self::PHOTO_FILE_REGEXP),
            array_values(self::PHOTO_FILE_REGEXP),
            $photoUrl
        );
    }

    public function addPhotoFile(): self
    {
        $this->photoFile = self::createPhotoFile($this->photoUrl);
        return $this;
    }

    public function photoFilePath(): string
    {
        $base = '/assets/images/ui-faces';
        return sprintf('%s/%s', $base, $this->photoFile);
    }
}
