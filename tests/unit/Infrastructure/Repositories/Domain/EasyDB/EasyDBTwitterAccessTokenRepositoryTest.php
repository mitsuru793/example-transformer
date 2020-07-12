<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\EasyDB;

use Php\Domain\Twitter\AccessToken\AccessToken;
use Php\Domain\Twitter\AccessToken\AccessTokenRepository;
use Php\Infrastructure\Tables\TwitterAccessTokenTable;

class EasyDBTwitterAccessTokenRepositoryTest extends TestCase
{
    private TwitterAccessTokenTable $table;

    private AccessTokenRepository $repo;

    protected function setUp(): void
    {
        parent::setUp();
        $this->table = new TwitterAccessTokenTable();
        $this->repo = new EasyDBTwitterAccessTokenRepository($this->db, $this->table);
    }


    public function testFindByTwitterUserId()
    {
        $f = $this->fixturesRow();
        $this->db->insertMany($this->table->name(), [
            $f['accessToken1'], $f['accessToken2'],
        ]);

        $got = $this->repo->findByTwitterUserId($f['accessToken1']['twitter_user_id']);
        $this->assertNotSame($f['accessToken2']['id'], $got->id);
        $this->assertEqualsToken($f['accessToken1'], $got);

        $got = $this->repo->findByTwitterUserId(999999999);
        $this->assertNull($got);
    }

    public function testFindByScreenName()
    {
        $f = $this->fixturesRow();
        $this->db->insertMany($this->table->name(), [
            $f['accessToken1'], $f['accessToken2'],
        ]);

        $got = $this->repo->findByScreenName($f['accessToken1']['screen_name']);
        $this->assertNotSame($f['accessToken2']['screen_name'], $got->screenName);
        $this->assertEqualsToken($f['accessToken1'], $got);

        $got = $this->repo->findByScreenName('not-found-9999999999999');
        $this->assertNull($got);
    }

    public function testCreateOrUpdate()
    {
        $f = $this->fixturesRow();

        $got = $this->db->find($this->table, $f['accessToken1']['id']);
        $this->assertNull($got);

        $f = $this->fixtures();
        $this->repo->createOrUpdate($f['accessToken1']);
        $got = $this->db->find($this->table, $f['accessToken1']->id);
        $this->assertEqualsToken($got, $f['accessToken1']);

        $f['accessToken1']->name = 'after';
        $got = $this->repo->createOrUpdate($f['accessToken1']);
        $this->assertEqualsToken($f['accessToken1'], $got);
    }

    /**
     * @param array|AccessToken $expected
     */
    private function assertEqualsToken($expected, AccessToken $actual): void
    {
        $value = $this->getValue($expected, 'id', 'id');
        $this->assertSame($value, $actual->id);

        $value = $this->getValue($expected, 'twitter_user_id', 'twitterUserId');
        $this->assertSame($value, $actual->twitterUserId);

        $value = $this->getValue($expected, 'screen_name', 'screenName');
        $this->assertSame($value, $actual->screenName);

        $value = $this->getValue($expected, 'token', 'token');
        $this->assertSame($value, $actual->token);

        $value = $this->getValue($expected, 'secret', 'secret');
        $this->assertSame($value, $actual->secret);
    }

    private function getValue($model, string $snakeCaseProp, string $camelCaseProp)
    {
        if (is_array($model)) {
            $t = $this->table->name();
            return $model[$snakeCaseProp] ?? $model["{$t}_$snakeCaseProp"];
        }
        if ($model instanceof AccessToken) {
            return $model->{$camelCaseProp};
        }
        $err = sprintf('Type of $model must be array of entity, but %s.', get_class($model));
        throw new \LogicException($err);
    }
}
