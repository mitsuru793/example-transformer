<?php
declare(strict_types=1);

namespace Php;

require_once __DIR__ . '/../vendor/autoload.php';

use League\Fractal;

function main()
{
    $faker = \Faker\Factory::create();

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
