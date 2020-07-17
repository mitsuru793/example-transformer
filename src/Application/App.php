<?php
declare(strict_types=1);

namespace Php\Application;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class App
{
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function process(ServerRequestInterface $request): ResponseInterface
    {
        $this->bootEloquent();

        $strategy = (new \League\Route\Strategy\ApplicationStrategy);
        $strategy->setContainer($this->container);

        $router = new \League\Route\Router;
        $router->setStrategy($strategy);

        $router->middlewares([
            $this->container->get(\Php\Application\Middlewares\LoginAuth::class),
            $this->container->get(\Php\Application\Middlewares\EnableCors::class),
        ]);

        $regexp = sprintf('@^%s$@', \Php\Library\Util\Host::api());
        if (preg_match($regexp, $request->getUri()->getHost())) {
            $add = require_once \Php\Library\Util\Path::root() . '/routes/api.php';
        } else {
            $add = require_once \Php\Library\Util\Path::root() . '/routes/web.php';
        }
        $add($router);

        $request = $this->extendFormHttpMethod($request);
        return $router->dispatch($request);
    }

    private function bootEloquent()
    {
        $capsule = new \Illuminate\Database\Capsule\Manager();
        $capsule->addConnection([
            'driver' => 'mysql',
            'host' => getenv('DB_HOST'),
            'database' => getenv('DB_DATABASE'),
            'username' => getenv('DB_USER'),
            'password' => getenv('DB_PASS'),
            'charset' => 'utf8',
            'collation' => 'utf8_unicode_ci',
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();
    }

    private function extendFormHttpMethod(ServerRequestInterface $request): ServerRequestInterface
    {
        $body = $request->getParsedBody();
        $formMethod = $body['_method'] ?? null;
        if ($request->getMethod() !== 'POST' || is_null($formMethod)) {
            return $request;
        }
        return $request->withMethod($formMethod);
    }
}