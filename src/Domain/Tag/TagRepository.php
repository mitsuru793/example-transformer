<?php
declare(strict_types=1);

namespace Php\Domain\Tag;

use Php\Domain\Post\Post;

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

    /**
     * @param Post[] $post
     * @return Post[]
     */
    public function findByPosts(array $posts): array;
}
