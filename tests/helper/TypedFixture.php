<?php
declare(strict_types=1);

namespace Helper;

use Php\Domain\Post\Post;
use Php\Domain\Tag\Tag;
use Php\Domain\User\User;
use Php\Library\Fixture\AliceFixture;

final class TypedFixture
{
    private AliceFixture $fixture;

    public function __construct(AliceFixture $fixture)
    {
        $this->fixture = $fixture;
    }

    public function user(int $key): User
    {
        return $this->get("user{$key}");
    }

    /**
     * @return User[]
     */
    public function users(string $key): array
    {
        return $this->get("user{{$key}}");
    }

    public function post(int $key): Post
    {
        return $this->get("post{$key}");
    }

    /**
     * @return Post[]
     */
    public function posts(string $key): array
    {
        return $this->get("post{{$key}}");
    }

    public function tag(int $key): Tag
    {
        return $this->get("tag{$key}");
    }

    /**
     * @return Tag[]
     */
    public function tags(string $key): array
    {
        return $this->get("tag{{$key}}");
    }

    /**
     * @return array|mixed
     */
    private function get(string $key)
    {
        $data = $this->fixture->get($key);
        if (!is_array($data)) {
            return $data;
        }

        // For insert db with easy-db.
        return array_values($data);
    }
}