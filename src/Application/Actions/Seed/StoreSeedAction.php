<?php
declare(strict_types=1);

namespace Php\Application\Actions\Seed;

use Faker\Factory;
use League\Plates\Engine;
use Php\Domain\Post\Post;
use Php\Domain\Post\PostRepository;
use Php\Domain\User\User;
use Php\Domain\User\UserRepository;
use Php\Infrastructure\Repositories\Domain\EasyDB\ExtendedEasyDB;
use Php\Infrastructure\Repositories\Domain\EasyDB\EasyDBPostRepository;
use Php\Infrastructure\Repositories\Domain\EasyDB\EasyDBUserRepository;
use Psr\Http\Message\ResponseInterface as Response;

final class StoreSeedAction extends SeedAction
{
    private UserRepository $userRepository;

    private PostRepository $postRepository;

    private ExtendedEasyDB $db;

    public function __construct(Engine $templates, UserRepository $userRepository, PostRepository $postRepository, ExtendedEasyDB $db)
    {
        parent::__construct($templates);
        $this->userRepository = $userRepository;
        $this->postRepository = $postRepository;
        $this->db = $db;
    }


    protected function action(): Response
    {
        $faker = Factory::create();
        $this->db->runSqlFile(__DIR__ . '/../../../../config/create_tables.sql');

        $users = [];
        for ($i = 0; $i < 30; $i++) {
            $user = new User(null, $faker->name);
            $users[] = $this->userRepository->create($user);
        }

        for ($i = 0; $i < 200; $i++) {
            $user = collect($users)->random();
            $content = implode('', $faker->sentences(5));
            $post = new Post(null, $user, $faker->sentence, $content, (int)$faker->year);
            $this->postRepository->create($post);
        }

        return $this->redirectBack($this->request, $this->response);
    }
}
