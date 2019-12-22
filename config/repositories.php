<?php
declare(strict_types=1);

use Php\Domain\Post\PostRepository;
use Php\Domain\User\UserRepository;
use Php\Infrastructure\Repositories\Domain\EasyDB\ExtendedEasyDB;
use Php\Infrastructure\Repositories\Domain\EasyDB\EasyDBPostRepository;
use Php\Infrastructure\Repositories\Domain\EasyDB\EasyDBUserRepository;
use Php\Infrastructure\Repositories\Domain\EasyDB\EasyDBTagRepository;

return function (\League\Container\Container $c) {
    $c->add(UserRepository::class, EasyDBUserRepository::class)
        ->addArgument(ExtendedEasyDB::class);

    $c->add(PostRepository::class, EasyDBPostRepository::class)
        ->addArgument(ExtendedEasyDB::class)
        ->addArgument(UserRepository::class);

    $c->add(\Php\Domain\Tag\TagRepository::class, EasyDBTagRepository::class)
        ->addArgument(ExtendedEasyDB::class);
};
