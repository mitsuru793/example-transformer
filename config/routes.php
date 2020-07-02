<?php
declare(strict_types=1);

return function (League\Route\Router $router) {
    $router->get('/', \Php\Application\Actions\Post\ListPostsAction::class);

    $router->post('/login', \Php\Application\Actions\Auth\LoginAction::class);
    $router->post('/logout', \Php\Application\Actions\Auth\LogoutAction::class);

    $router->group('/posts', function (\League\Route\RouteGroup $r) {
        $r->get('/{postId}', \Php\Application\Actions\Post\ShowPostAction::class);
        $r->get('/{postId}/edit', \Php\Application\Actions\Post\EditPostAction::class);
        $r->put('/{postId}', \Php\Application\Actions\Post\UpdatePostAction::class);
    });

    $router->group('/seeds', function (\League\Route\RouteGroup $r) {
        $r->post('/', \Php\Application\Actions\Seed\StoreSeedAction::class);
    });

    $router->group('/UIFacesUsers', function (\League\Route\RouteGroup $r) {
        $r->get('/', \Php\Application\Actions\UIFacesUser\ListUIFacesUsersAction::class);
    });
    $router->post('/UIFacesUsers:fetch', \Php\Application\Actions\UIFacesUser\RequestGetApiAction::class);
};
