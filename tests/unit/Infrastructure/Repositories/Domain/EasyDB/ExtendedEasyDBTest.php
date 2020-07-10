<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\EasyDB;

use Php\Infrastructure\Tables\Table;

final class ExtendedEasyDBTest extends TestCase
{
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
