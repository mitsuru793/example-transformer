<?php
declare(strict_types=1);

namespace Php;

require_once __DIR__ . '/../vendor/autoload.php';

use League\Container\Container;
use League\Fractal;
use Php\Domain\Book\Book;
use Php\Domain\Book\BookTransformer;
use Php\Domain\User\User;

function main()
{
    $faker = \Faker\Factory::create();
    $container = new Container();
    $container->bind(BookTransformer::class, BookTransformer::class);
    $container->get(BookTransformer::class);

    $fractal = new Fractal\Manager();
    $fractal->parseIncludes('author');

    $book = Book::fake($faker);
    $viewableUserIds = [1, 2, 3];
    $book->viewableUserIds = $viewableUserIds;

    $viewer = User::fake($faker);
    $viewer->id = 1;

    $resource = new Fractal\Resource\Item($book, new BookTransformer($viewer));

    $data = $fractal->createData($resource)->toArray();
    dump($data);
}

main();
