<?php
declare(strict_types=1);

namespace Php\Application\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class Command extends \Symfony\Component\Console\Command\Command
{
    protected const ROOT = __DIR__ . '/../../..';
}