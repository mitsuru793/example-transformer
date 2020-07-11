<?php
declare(strict_types=1);

use Php\Domain\Post\PostRepository;
use Php\Domain\Tag\TagRepository;
use Php\Domain\Twitter\AccessToken\AccessTokenRepository;
use Php\Domain\UIFacesUser\UIFacesUserRepository;
use Php\Domain\User\UserRepository;
use Php\Infrastructure\Repositories\Domain\EasyDB\EasyDBPostRepository;
use Php\Infrastructure\Repositories\Domain\EasyDB\EasyDBTagRepository;
use Php\Infrastructure\Repositories\Domain\EasyDB\EasyDBTwitterAccessTokenRepository;
use Php\Infrastructure\Repositories\Domain\EasyDB\EasyDBUserRepository;
use Php\Infrastructure\Repositories\Domain\Eloquent\EloquentUIFacesUserRepository;

return function (\League\Container\Container $c) {
    $c->add(UserRepository::class, fn() => $c->get(EasyDBUserRepository::class));
    $c->add(PostRepository::class, fn() => $c->get(EasyDBPostRepository::class));
    $c->add(TagRepository::class, fn() => $c->get(EasyDBTagRepository::class));
    $c->add(UIFacesUserRepository::class, fn() => $c->get(EloquentUIFacesUserRepository::class));
    $c->add(AccessTokenRepository::class, fn() => $c->get(EasyDBTwitterAccessTokenRepository::class));
};
