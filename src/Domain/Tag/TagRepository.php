<?php
declare(strict_types=1);

namespace Php\Domain\Tag;

interface TagRepository
{
    public function create(Tag $tag): Tag;

    /**
     * @return Tag[]
     */
    public function findRandoms(int $count): array;

    /**
     * @return Tag[]
     */
    public function findByPostId(int $postId): array;
}
