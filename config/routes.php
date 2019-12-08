<?php
declare(strict_types=1);


return function (League\Route\Router $router) {
    $router->get('/', \Php\Application\Actions\Post\ListPostsAction::class);

    $router->group('/seeds', function (\League\Route\RouteGroup $r) {
        $r->post('/', \Php\Application\Actions\Seed\StoreSeedAction::class);
    });
};
