<?php
declare(strict_types=1);

namespace Php\Domain\User;

use League\Fractal\TransformerAbstract;

final class UserTransformer extends TransformerAbstract
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
