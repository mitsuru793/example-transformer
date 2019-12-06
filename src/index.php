<?php
declare(strict_types=1);

namespace Php;

require_once __DIR__ . '/../vendor/autoload.php';

use League\Container\Container;
use League\Fractal;
use Php\Domain\Post\Post;
use Php\Domain\Post\PostTransformer;
use Php\Domain\User\User;

function main()
{
    $faker = \Faker\Factory::create();
    $container = new Container();
    $container->bind(PostTransformer::class, PostTransformer::class);
    $container->get(PostTransformer::class);

    $fractal = new Fractal\Manager();
    $fractal->parseIncludes('author');

    $post = Post::fake($faker);
    $viewableUserIds = [1, 2, 3];
    $post->viewableUserIds = $viewableUserIds;

    $viewer = User::fake($faker);
    $viewer->id = 1;

    $resource = new Fractal\Resource\Item($post, new PostTransformer($viewer));

    $data = $fractal->createData($resource)->toArray();
    dump($data);
}

main();
