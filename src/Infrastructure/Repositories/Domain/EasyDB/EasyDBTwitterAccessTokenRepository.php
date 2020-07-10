<?php
declare(strict_types=1);

namespace Php\Infrastructure\Repositories\Domain\EasyDB;

use Php\Domain\Twitter\AccessToken\AccessToken;
use Php\Domain\Twitter\AccessToken\AccessTokenRepository;

final class EasyDBTwitterAccessTokenRepository implements AccessTokenRepository
{
    private ExtendedEasyDB $db;

    public function __construct(ExtendedEasyDB $db)
    {
        $this->db = $db;
    }

    public function findByTwitterUserId(int $userId): ?AccessToken
    {
        $row = $this->db->row(<<<SQL
            SELECT {$this->columnsStr()}
            FROM twitter_oauth_access_tokens
            WHERE twitter_oauth_access_tokens.twitter_user_id = ?
            SQL,
            $userId,
        );
        if (!$row) {
            return null;
        }
        return $this->toAccessToken($row);
    }

    public function findByScreenName(string $name): ?AccessToken
    {
        $row = $this->db->row(<<<SQL
            SELECT {$this->columnsStr()}
            FROM twitter_oauth_access_tokens
            WHERE twitter_oauth_access_tokens.screen_name = ?
            SQL,
            $name,
        );
        if (!$row) {
            return null;
        }
        return $this->toAccessToken($row);
    }

    public function createOrUpdate(AccessToken $token): AccessToken
    {
        $found = $this->findByTwitterUserId($token->twitterUserId);
        if (!$found) {
            $this->db->insert('twitter_oauth_access_tokens', [
                'twitter_user_id' => $token->twitterUserId,
                'screen_name' => $token->screenName,
                'token' => $token->token,
                'secret' => $token->secret,
            ]);
            $token->id = (int)$this->db->lastInsertId();
            return $token;
        }

        $this->db->update('twitter_oauth_access_tokens', [
            'screen_name' => $token->screenName,
            'token' => $token->token,
            'secret' => $token->secret,
        ], [
            'twitter_user_id' => $token->twitterUserId,
        ]);
        $token->id = $found->id;
        return $token;
    }

    public function columns(): array
    {
        $columns = ['id', 'twitter_user_id', 'screen_name', 'token', 'secret'];
        return array_map(fn ($v) => "twitter_oauth_access_tokens.$v AS twitter_oauth_access_tokens_$v", $columns);
    }

    public function columnsStr(): string
    {
        return implode(', ', $this->columns());
    }

    public function toAccessToken(array $row): AccessToken
    {
        $table = 'twitter_oauth_access_tokens';
        return new AccessToken(
            (int)($row["{$table}_id"] ?? $this->db->lastInsertId()),
            $row["{$table}_token"],
            $row["{$table}_secret"],
            (int)$row["{$table}_twitter_user_id"],
            $row["{$table}_screen_name"],
        );
    }
}
