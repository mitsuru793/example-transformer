<?php
declare(strict_types=1);

namespace Php\Application\Actions\Post;

use League\Plates\Engine;
use Php\Domain\Post\PostRepository;
use Php\Domain\Post\PostTransformer;
use Php\Domain\Tag\TagRepository;
use Php\Domain\User\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;

final class UpdatePostAction extends PostAction
{
    private UserRepository $userRepository;

    private PostRepository $postRepo;

    private PostTransformer $postTransformer;

    private TagRepository $tagRepo;

    public function __construct(Engine $templates, PostRepository $postRepo, PostTransformer $postTransformer, UserRepository $userRepository, TagRepository $tagRepo)
    {
        parent::__construct($templates);
        $this->userRepository = $userRepository;
        $this->postRepo = $postRepo;
        $this->postTransformer = $postTransformer;
        $this->tagRepo = $tagRepo;
    }

    /**
     * @throws \League\Route\Http\Exception\BadRequestException
     */
    protected function action(): Response
    {
        $postId = (int)$this->resolveArg('postId');
        $post = $this->postRepo->find($postId);

        $body = $this->request->getParsedBody();
        $post->title = $body['title'];
        $post->content = $body['content'];
        $this->postRepo->store($post);

        $tagNames = explode(',', $body['tags']);
        $tags = $this->tagRepo->findOrCreateMany($tagNames);
        $this->postRepo->updateTags($post->id, $tags);

        return $this->response->withStatus(301)->withHeader('Location', "/posts/$post->id");
    }
}
