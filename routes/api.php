<?php
declare(strict_types=1);

use Php\Application\Actions\Auth;
use Php\Application\Actions\Debug\RenderStringAction;
use Php\Application\Api\Actions\User\CreateUserAction;
use Php\Application\Api\Actions\User\DeleteUserAction;
use Php\Application\Api\Actions\User\ListUsersAction;
use Php\Application\Api\Actions\User\ShowUserAction;
use Php\Application\Api\Actions\User\UpdateUserAction;

return function (\League\Route\Router $router) {
    $router->options('/{routes:.*}', Auth\PermitPreflightAction::class);

    $router->get('/', new RenderStringAction('welcome!'));

    $router->get('/string', new RenderStringAction('get string'));
    $router->post('/string', new RenderStringAction('post string'));
    $router->put('/string', new RenderStringAction('put string'));
    $router->delete('/string', new RenderStringAction('delete string'));

    $router->group('/users', function (\League\Route\RouteGroup $r) {
        $r->get('/', ListUsersAction::class);
        $r->post('/', CreateUserAction::class);
        $r->get('/{userId:\d+}', ShowUserAction::class);
        $r->put('/{userId:\d+}', UpdateUserAction::class);
        $r->delete('/{userId:\d+}', DeleteUserAction::class);
    });
};
