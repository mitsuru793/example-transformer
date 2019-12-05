<?php
declare(strict_types=1);

namespace Php\Domain\Book;

use League\Fractal\TransformerAbstract;
use Php\Domain\User\User;
use Php\Domain\User\UserTransformer;

final class BookTransformer extends TransformerAbstract
{
    protected $availableIncludes = [
        'author'
    ];

    private User $viewer;

    public function setViewer(User $viewer)
    {

        $this->viewer = $viewer;
    }

    public function transform(Book $book): array
    {
        return [
            'id'    => (int) $book->id,
            'title' => $book->title,
            'year'    => (int) $book->year,
            'viewable' => in_array($this->viewer->id ?? null, $book->viewableUserIds),
            'links'   => [
                [
                    'rel' => 'self',
                    'uri' => '/books/'.$book->id,
                ]
            ],
        ];
    }

    public function includeAuthor(Book $book): \League\Fractal\Resource\Item
    {
        $author = $book->author;

        return $this->item($author, new UserTransformer);
    }
}
