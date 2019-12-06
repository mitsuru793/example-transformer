<?php
declare(strict_types=1);

namespace Php\Domain\Post;

use League\Fractal\TransformerAbstract;
use Php\Domain\User\User;
use Php\Domain\User\UserTransformer;

final class PostTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'author',
    ];

    private User $viewer;

    public function setViewer(User $viewer): self
    {
        $this->viewer = $viewer;
        return $this;
    }

    public function transform(Post $post): array
    {
        return [
            'id' => (int)$post->id,
            'title' => $post->title,
            'year' => (int)$post->year,
            'viewable' => in_array($this->viewer->id ?? null, $post->viewableUserIds),
            'links' => [
                [
                    'rel' => 'self',
                    'uri' => '/posts/' . $post->id,
                ],
            ],
        ];
    }

    public function includeAuthor(Post $post): \League\Fractal\Resource\Item
    {
        $author = $post->author;

        return $this->item($author, new UserTransformer);
    }
}
