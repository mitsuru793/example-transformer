<?php
declare(strict_types=1);

namespace Php\Domain\Post;

use Php\Domain\Tag\Tag;
use Php\Domain\User\User;

final class Post
{
    public ?int $id;

    public string $title;

    public string $content;

    public int $year;

    public User $author;

    /** @var Tag[] */
    public array $tags;

    public array $viewableUserIds;

    public function __construct(?int $id, User $author, string $title, string $content, int $year, array $viewableUserIds = [])
    {
        $this->id = $id;
        $this->title = $title;
        $this->year = $year;
        $this->author = $author;
        $this->viewableUserIds = $viewableUserIds;
        $this->content = $content;
    }

    /**
     * @param Tag[] $tags
     */
    public function addTags(array $tags): self
    {
        $this->tags = $tags;
        return $this;
    }
}
