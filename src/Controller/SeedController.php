<?php
declare(strict_types=1);

namespace Php\Controller;

use Faker\Factory;
use League\Plates\Engine;
use Php\Domain\Post\Post;
use Php\Domain\User\User;
use Php\Infrastructure\PostRepository;
use Php\Infrastructure\Database;
use Php\Infrastructure\UserRepository;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class SeedController extends Controller
{
    private Database $db;

    private UserRepository $userRepository;

    private PostRepository $postRepository;

    public function __construct(Engine $templates, Database $db, UserRepository $userRepository, PostRepository $postRepository)
    {
        parent::__construct($templates);
        $this->db = $db;
        $this->userRepository = $userRepository;
        $this->postRepository = $postRepository;
    }

    public function store(ServerRequestInterface $req): ResponseInterface
    {
        $faker = Factory::create();
        $this->db->runSqlFile(__DIR__ . '/../../config/create_tables.sql');

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

        return $this->redirectBack($req, $this->response);
    }
}
