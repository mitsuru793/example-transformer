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

    public function find(int $tagId): ?Tag;

    /**
     * @param string[] $names
     * @return Tag[]
     */
    public function findByNames(array $names): array;

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

    /**
     * @return Tag[]
     */
    public function paging(int $page, int $perPage): array;
}
