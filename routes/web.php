<?php
declare(strict_types=1);

use Php\Application\Actions\Auth;
use Php\Application\Actions\Debug;
use Php\Application\Actions\Post;
use Php\Application\Actions\Seed;
use Php\Application\Actions\Tweet;
use Php\Application\Actions\Twitter;
use Php\Application\Actions\UIFacesUser;

return function (League\Route\Router $router, \Psr\Container\ContainerInterface $container) {
    $router->get('/', Post\ListPostsAction::class);

    $router->post('/login', Auth\LoginAction::class);
    $router->post('/logout', Auth\LogoutAction::class);

    $router->group('/posts', function (\League\Route\RouteGroup $r) {
        $r->get('/{postId}', Post\ShowPostAction::class);
        $r->get('/{postId}/edit', Post\EditPostAction::class);
        $r->put('/{postId}', Post\UpdatePostAction::class);
    });

    $router->group('/seeds', function (\League\Route\RouteGroup $r) {
        $r->post('/', Seed\StoreSeedAction::class);
    });

    $router->group('/UIFacesUsers', function (\League\Route\RouteGroup $r) {
        $r->get('/', UIFacesUser\ListUIFacesUsersAction::class);
    });
    $router->post('/UIFacesUsers:fetch', UIFacesUser\RequestGetApiAction::class);

    $router->group('/twitter', function (\League\Route\RouteGroup $r) {
        $r->get('/oauth1/login', Twitter\LoginTwitterAction::class);
        $r->get('/oauth_callback', Twitter\CallbackAction::class);

        $r->get('/users/{name:\w+}/home', Tweet\ListUserHome::class);
    });
    $router->group('/tweets', function (\League\Route\RouteGroup $r) {
        $r->get('/home', Tweet\ListMyHomeAction::class);
    });

    $router->group('/debug', function (\League\Route\RouteGroup $r) use ($container) {
        $templates = $container->get(\League\Plates\Engine::class);
        $r->get('/cors-request', new Debug\RenderStaticPageAction($templates, 'debug/cors-request'));

        $r->get('/openapi-client', new Debug\RenderStaticPageAction($templates, 'debug/openapi-client'));
    });
};
