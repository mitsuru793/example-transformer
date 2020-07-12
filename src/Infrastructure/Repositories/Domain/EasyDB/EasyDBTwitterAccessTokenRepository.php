<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\EasyDB;

use Php\Domain\Twitter\AccessToken\AccessToken;
use Php\Domain\Twitter\AccessToken\AccessTokenRepository;
use Php\Infrastructure\Tables\TwitterAccessTokenTable;

final class EasyDBTwitterAccessTokenRepository implements AccessTokenRepository
{
    private ExtendedEasyDB $db;

    private TwitterAccessTokenTable $table;

    public function __construct(ExtendedEasyDB $db, TwitterAccessTokenTable $table)
    {
        $this->db = $db;
        $this->table = $table;
    }

    public function findByTwitterUserId(int $userId): ?AccessToken
    {
        $row = $this->db->row(<<<SQL
            SELECT {$this->table->columnsStr()}
            FROM {$this->table->name()}
            WHERE {$this->table->name()}.twitter_user_id = ?
            SQL,
            $userId,
        );
        if (!$row) {
            return null;
        }
        return $this->toEntity($row);
    }

    public function findByScreenName(string $name): ?AccessToken
    {
        $row = $this->db->row(<<<SQL
            SELECT {$this->table->columnsStr()}
            FROM twitter_oauth_access_tokens
            WHERE twitter_oauth_access_tokens.screen_name = ?
            SQL,
            $name,
        );
        if (!$row) {
            return null;
        }
        return $this->toEntity($row);
    }

    public function createOrUpdate(AccessToken $token): AccessToken
    {
        $found = $this->findByTwitterUserId($token->twitterUserId);
        if (!$found) {
            $this->db->insert($this->table->name(), [
                'twitter_user_id' => $token->twitterUserId,
                'screen_name' => $token->screenName,
                'token' => $token->token,
                'secret' => $token->secret,
            ]);
            $token->id = (int)$this->db->lastInsertId();
            return $token;
        }

        $this->db->update($this->table->name(), [
            'screen_name' => $token->screenName,
            'token' => $token->token,
            'secret' => $token->secret,
        ], [
            'twitter_user_id' => $token->twitterUserId,
        ]);
        $token->id = $found->id;
        return $token;
    }

    public function toEntity(array $row): AccessToken
    {
        $table = $this->table->name();
        return new AccessToken(
            (int)($row["{$table}_id"] ?? $this->db->lastInsertId()),
            $row["{$table}_token"],
            $row["{$table}_secret"],
            (int)$row["{$table}_twitter_user_id"],
            $row["{$table}_screen_name"],
        );
    }
}
