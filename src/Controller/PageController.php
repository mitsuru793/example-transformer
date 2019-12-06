<?php
declare(strict_types=1);

namespace Php\Controller;

use League\Fractal\Manager;
use League\Fractal\Pagination\Cursor;
use League\Fractal\Resource\Collection;
use League\Plates\Engine;
use Php\Domain\Book\BookTransformer;
use Php\Infrastructure\BookRepository;
use Php\Infrastructure\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class PageController extends Controller
{
    private UserRepository $userRepository;

    private BookRepository $bookRepo;

    private BookTransformer $bookTransformer;

    public function __construct(Engine $templates, BookRepository $bookRepo, BookTransformer $bookTransformer, UserRepository $userRepository)
    {
        parent::__construct($templates);
        $this->userRepository = $userRepository;
        $this->bookRepo = $bookRepo;
        $this->bookTransformer = $bookTransformer;
    }

    public function index(ServerRequestInterface $req): ResponseInterface
    {
        $loginUser = $this->userRepository->find(1);
        $query = $req->getQueryParams();
        $page = (int)($query['page'] ?? 1);
        $perPage = 5;

        $booksCount = $this->bookRepo->count();
        $books = $this->bookRepo->paging($page, $perPage);
        $lastPage = ceil($booksCount / $perPage);

        $fractal = new Manager();
        $fractal->parseIncludes('author');
        $this->bookTransformer->setViewer($loginUser);
        $resource = new Collection($books, $this->bookTransformer, 'books');
        $cursor = new Cursor($page, 100, end($books)->id, count($books));
        $resource->setCursor($cursor);
        $transformed = $fractal->createData($resource)->toArray();

        $pager = $this->pager($booksCount, $books)
            ->setMaxPerPage($perPage)
            ->setCurrentPage($page);
        $pagerHtml = $this->pagerHtml($pager, fn($page) => "/?page=$page&perPage=$perPage");
        return $this->view($this->response, 'index', compact(
            'loginUser', 'booksCount', 'books', 'page', 'lastPage', 'pager', 'pagerHtml', 'transformed'
        ));
    }
}
