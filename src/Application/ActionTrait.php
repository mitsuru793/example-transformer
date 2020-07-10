<?php
declare(strict_types=1);

namespace Php\Application;

use League\Route\Http\Exception\BadRequestException;
use League\Route\Http\Exception\NotFoundException;
use Php\Application\Middlewares\LoginAuth;
use Php\Domain\DomainException\DomainRecordNotFoundException;
use Php\Domain\User\User;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Log\LoggerInterface;

trait ActionTrait
{
    /** @var LoggerInterface */
    protected $logger;

    /** @var Request */
    protected $request;

    /** @var Response */
    protected $response;

    /** @var array */
    protected $args;

    /** @var User */
    protected $loginUser;

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

}
