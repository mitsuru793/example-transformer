<?php
declare(strict_types=1);

namespace Php\Domain\Tag;

use Php\Domain\Post\Post;

interface TagRepository
{
    public function create(Tag $tag): Tag;

    /**
     * @param Tag[] $tag
     */
    public function createMany(array $tag): void;

    /**
     * @param string $tagNames
     * @return Tag[]
     */
    public function findOrCreateMany(array $tagNames): array;

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

    public function findByPost(Post $post): Post;
}
