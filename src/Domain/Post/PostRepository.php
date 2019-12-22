<?php
declare(strict_types=1);

namespace Php\Domain\Post;

use Php\Domain\Tag\Tag;

interface PostRepository
{
    public function create(Post $post): Post;

    public function find(int $postId): Post;

    /**
     * @param Tag[] $tags
     */
    public function addTags(int $postId, array $tags): void;

    public function paging(int $page, int $perPage): array;
}
