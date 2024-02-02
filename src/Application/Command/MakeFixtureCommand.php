<?php
declare(strict_types=1);

namespace Php\Application\Command;

use Php\Domain\User\UserRepository;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class MakeFixtureCommand
{
    protected static $defaultName = 'init:db';

    private UserRepository $db;

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->db->runSqlFile(self::ROOT . '/config/create_tables.sql');
        return Command::SUCCESS;
    }
}
