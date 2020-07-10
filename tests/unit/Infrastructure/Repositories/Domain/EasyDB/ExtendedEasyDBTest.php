<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\EasyDB;

use Php\Infrastructure\Tables\Table;
use PHPUnit\Framework\TestCase;

final class ExtendedEasyDBTest extends TestCase
{
    private ExtendedEasyDB $db;

    protected function setUp(): void
    {
        parent::setUp();
        $pdo = new \PDO(
            sprintf('mysql:host=%s;dbname=%s', getenv('TEST_DB_HOST'), getenv('TEST_DB_DATABASE')),
            getenv('TEST_DB_USER'),
            getenv('TEST_DB_PASS'),
        );
        $this->db = new ExtendedEasyDB($pdo, 'mysql');
    }

    public function testFind()
    {
        $this->db->insertMany('users', [
            ['id' => 1, 'name' => 'n1'],
            ['id' => 2, 'name' => 'n2'],
            ['id' => 3, 'name' => 'n3'],
        ]);
        $table = new class implements Table {
            public function name(): string
            {
                return 'users';
            }

            public function columns(): array
            {
                return ['id', 'name'];
            }
        };
        $row = $this->db->find($table, 2);
        $this->assertSame(2, $row['id']);
        $this->assertSame('n2', $row['name']);
    }
}
