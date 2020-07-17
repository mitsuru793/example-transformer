<?php
declare(strict_types=1);

namespace FunctionalTest\Http\Api;

use Helper\FixtureTrait;
use Php\Infrastructure\Repositories\Domain\EasyDB\ExtendedEasyDB;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\ServerRequestFactory;

abstract class TestCase extends \PHPUnit\Framework\TestCase
{
    use FixtureTrait;

    private \Php\Application\App $app;

    private \Php\Library\Fixture\Loader $loader;

    protected ExtendedEasyDB $db;

    private ContainerInterface $container;

    protected function setUp(): void
    {
        parent::setUp();
        $this->app = $this->createApp();
        $this->loader = new \Php\Library\Fixture\Loader(__DIR__ . '/../../../fixtures.yml');

        $this->db = $this->container->get(ExtendedEasyDB::class);
        $this->db->beginTransaction();
    }

    protected function tearDown(): void
    {
        if ($this->db->inTransaction()) {
            $this->db->rollback();
        }
    }

    public function loader(): \Php\Library\Fixture\Loader
    {
        return $this->loader;
    }

    protected function http(string $method, string $path, array $params = []): ResponseInterface
    {
        $factory = new ServerRequestFactory();
        $request = $factory->createServerRequest($method, $path);
        $uri = $request->getUri()->withHost(\Php\Library\Util\Host::api());
        $request = $request->withUri($uri);

        if (strtolower($method) === 'get') {
            $request = $request->withQueryParams($params);
        } else {
            $request = $request->withParsedBody($params);
        }

        return $this->app->process($request);
    }

    private function createApp(): \Php\Application\App
    {
        $this->container = new \League\Container\Container;
        $this->container->delegate(new \League\Container\ReflectionContainer);

        $add = require __DIR__ . '/../../../../config/dependencies.php';
        $add($this->container);

        $add = require __DIR__ . '/../../../../config/repositories.php';
        $add($this->container);

        return new \Php\Application\App($this->container);
    }
}