<?php
declare(strict_types=1);

use Php\Domain\Post\PostRepository;
use Php\Domain\User\UserRepository;
use Php\Infrastructure\Repositories\Domain\EasyDB\EasyDBPostRepository;
use Php\Infrastructure\Repositories\Domain\EasyDB\EasyDBTagRepository;
use Php\Infrastructure\Repositories\Domain\EasyDB\EasyDBTwitterAccessTokenRepository;
use Php\Infrastructure\Repositories\Domain\EasyDB\EasyDBUserRepository;
use Php\Infrastructure\Repositories\Domain\EasyDB\ExtendedEasyDB;
use Php\Infrastructure\Repositories\Domain\Eloquent\EloquentUIFacesUserRepository;
use Php\Infrastructure\Tables\UserTable;

return function (\League\Container\Container $c) {
    $c->add(UserRepository::class, EasyDBUserRepository::class)
        ->addArgument(ExtendedEasyDB::class)
        ->addArgument(UserTable::class);

    $c->add(PostRepository::class, EasyDBPostRepository::class)
        ->addArgument(ExtendedEasyDB::class)
        ->addArgument(UserRepository::class);

    $c->add(\Php\Domain\Tag\TagRepository::class, EasyDBTagRepository::class)
        ->addArgument(ExtendedEasyDB::class);

    $c->add(\Php\Domain\UIFacesUser\UIFacesUserRepository::class, EloquentUIFacesUserRepository::class);

    $c->add(\Php\Domain\Twitter\AccessToken\AccessTokenRepository::class, EasyDBTwitterAccessTokenRepository::class)
        ->addArgument(ExtendedEasyDB::class);
};
