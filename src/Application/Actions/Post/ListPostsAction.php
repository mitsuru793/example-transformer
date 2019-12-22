<?php
declare(strict_types=1);

namespace Php\Application\Actions\Post;

use League\Fractal\Manager;
use League\Fractal\Pagination\Cursor;
use League\Fractal\Resource\Collection;
use League\Plates\Engine;
use Php\Domain\Post\PostRepository;
use Php\Domain\Post\PostTransformer;
use Php\Domain\User\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;

final class ListPostsAction extends PostAction
{
    private UserRepository $userRepository;

    private PostRepository $postRepo;

    private PostTransformer $postTransformer;

    public function __construct(Engine $templates, PostRepository $postRepo, PostTransformer $postTransformer, UserRepository $userRepository)
    {
        parent::__construct($templates);
        $this->userRepository = $userRepository;
        $this->postRepo = $postRepo;
        $this->postTransformer = $postTransformer;
    }

    protected function action(): Response
    {
        $loginUser = $this->userRepository->find(1);
        $query = $this->request->getQueryParams();
        $page = (int)($query['page'] ?? 1);
        $perPage = 5;

        $postsCount = $this->postRepo->count();
        $posts = $this->postRepo->paging($page, $perPage);
        $lastPage = ceil($postsCount / $perPage);

        $fractal = new Manager();
        $fractal->parseIncludes('author');
        if (!is_null($loginUser)) {
            $this->postTransformer->setViewer($loginUser);
        }
        $resource = new Collection($posts, $this->postTransformer, 'posts');
        if (empty($posts)) {
            $cursor = new Cursor($page, 100);
        } else {
            $cursor = new Cursor($page, 100, end($posts)->id, count($posts));
        }
        $resource->setCursor($cursor);
        $transformed = $fractal->createData($resource)->toArray();

        $pager = $this->pager($postsCount, $posts)
            ->setMaxPerPage($perPage)
            ->setCurrentPage($page);
        $pagerHtml = $this->pagerHtml($pager, fn($page) => "/?page=$page&perPage=$perPage");
        return $this->renderView($this->response, 'index', compact(
            'loginUser', 'postsCount', 'posts', 'page', 'lastPage', 'pager', 'pagerHtml', 'transformed'
        ));
    }
}
