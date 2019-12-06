<?php
declare(strict_types=1);

namespace Php\Application\Actions;

use League\Plates\Engine;
use League\Plates\Template;
use League\Route\Http\Exception\BadRequestException;
use League\Route\Http\Exception\NotFoundException;
use Pagerfanta\Adapter\FixedAdapter;
use Pagerfanta\Pagerfanta;
use Php\Application\Middlewares\LoginAuth;
use Php\Domain\DomainException\DomainRecordNotFoundException;
use Php\Domain\User\User;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

abstract class Action
{
    /** @var LoggerInterface */
    protected $logger;

    /** @var Template */
    protected $templates;

    /** @var Request */
    protected $request;

    /** @var User */
    protected $loginUser;

    /** @var Response */
    protected $response;

    /** @var array */
    protected $args;

    public function __construct(Engine $templates)
    {
        $this->templates = $templates;
    }

    /**
     * @throws NotFoundException
     */
    public function __invoke(Request $request, array $args): Response
    {
        $this->request = $request;
        $this->response = new \Zend\Diactoros\Response();
        $this->args = $args;
        $this->loginUser = $request->getAttribute(LoginAuth::ATTRIBUTE_KEY);
        try {
            return $this->action();
        } catch (DomainRecordNotFoundException $e) {
            throw new NotFoundException($e->getMessage());
        }
    }

    /**
     * @throws DomainRecordNotFoundException
     */
    abstract protected function action(): Response;

    /**
     * @return array|object
     * @throws BadRequestException
     */
    protected function getFormData()
    {
        $input = json_decode(file_get_contents('php://input'));
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new BadRequestException('Malformed JSON input.');
        }
        return $input;
    }

    /**
     * @return mixed
     * @throws BadRequestException
     */
    protected function resolveArg(string $name)
    {
        if (!isset($this->args[$name])) {
            throw new BadRequestException("Could not resolve argument `{$name}`.");
        }
        return $this->args[$name];
    }

    /**
     * @param array|object|null $data
     */
    protected function respondWithData($data = null): Response
    {
        $payload = new ActionPayload(200, $data);
        return $this->respond($payload);
    }

    protected function respond(ActionPayload $payload): Response
    {
        $json = json_encode($payload, JSON_PRETTY_PRINT);
        $this->response->getBody()->write($json);
        return $this->response->withHeader('Content-Type', 'application/json');
    }

    protected function renderView(Response $response, string $template, array $data = []): Response
    {
        $output = $this->templates->render($template, $data);
        $response->getBody()->write($output);
        return $response;
    }

    protected function pager(int $totalCount, array $items): Pagerfanta
    {
        $adapter = new FixedAdapter($totalCount, $items);
        return new Pagerfanta($adapter);
    }

    protected function pagerHtml(Pagerfanta $pager, callable $routeGenerator): string
    {
        $pagerView = new \Pagerfanta\View\TwitterBootstrap4View();
        return $pagerView->render($pager, $routeGenerator);
    }

    protected function redirectBack(RequestInterface $req, Response $res): Response
    {
        $referrer = $req->getServerParams()['HTTP_REFERER'];
        return $res
            ->withStatus(303)
            ->withHeader('Location', $referrer);
    }
}
