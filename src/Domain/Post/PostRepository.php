<?php
declare(strict_types=1);

namespace Php\Domain\Post;

interface PostRepository
{
    public function create(Post $post): Post;
    
    public function paging(int $page, int $perPage): array;
}
