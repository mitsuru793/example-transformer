<?php
declare(strict_types=1);

namespace Php\Controller;

use League\Plates\Engine;
use Php\Infrastructure\BookRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class PageController extends Controller
{
    private BookRepository $bookRepo;

    public function __construct(Engine $templates, BookRepository $bookRepo)
    {
        parent::__construct($templates);
        $this->bookRepo= $bookRepo;
    }

    public function index(ServerRequestInterface $req): ResponseInterface
    {
        $query = $req->getQueryParams();
        $page = (int)($query['page'] ?? 1);
        $perPage = 5;

        $booksCount = $this->bookRepo->count();
        $books = $this->bookRepo->paging($page, $perPage);
        $lastPage = ceil($booksCount / $perPage);

        $pager = $this->pager($booksCount, $books)
            ->setMaxPerPage($perPage)
            ->setCurrentPage($page);
        $pagerHtml = $this->pagerHtml($pager, fn($page) => "/?page=$page&perPage=$perPage");
        return $this->view($this->response, 'index', compact(
            'booksCount', 'books', 'page', 'lastPage', 'pager', 'pagerHtml'
        ));
    }
}
