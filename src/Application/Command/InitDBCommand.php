<?php
declare(strict_types=1);

namespace Php\Application\Command;

use Php\Infrastructure\Repositories\Domain\EasyDB\ExtendedEasyDB;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

final class InitDBCommand extends Command
{
    protected static $defaultName = 'init:db';

    private ExtendedEasyDB $db;

    public function __construct(ExtendedEasyDB $db)
    {
        $this->db = $db;
        parent::__construct(null);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->db->runSqlFile(self::ROOT . '/config/create_tables.sql');
        return Command::SUCCESS;
    }
}