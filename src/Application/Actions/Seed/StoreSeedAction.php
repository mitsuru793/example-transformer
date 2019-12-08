<?php
declare(strict_types=1);

namespace Php\Application\Actions\Seed;

use Faker\Factory;
use League\Plates\Engine;
use Php\Domain\Post\Post;
use Php\Domain\User\User;
use Php\Infrastructure\Database;
use Php\Infrastructure\PostRepository;
use Php\Infrastructure\UserRepository;
use Psr\Http\Message\ResponseInterface as Response;

final class StoreSeedAction extends SeedAction
{
    private UserRepository $userRepository;

    private PostRepository $postRepository;

    private Database $db;

    public function __construct(Engine $templates, UserRepository $userRepository, PostRepository $postRepository, Database $db)
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
            $post = new Post(null, $faker->sentence, (int)$faker->year, $user);
            $this->postRepository->create($post);
        }

        return $this->redirectBack($this->request, $this->response);
    }
}
