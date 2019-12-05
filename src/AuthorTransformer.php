<?php
declare(strict_types=1);

namespace Php;

use League\Fractal\TransformerAbstract;

final class AuthorTransformer extends TransformerAbstract
{
    public function transform(User $author): array
    {
        return [
            'id'    => (int) $author->id,
            'name' => $author->name,
            'links'   => [
                [
                    'rel' => 'self',
                    'uri' => '/users/'.$author->id,
                ]
            ],
        ];
    }
}
