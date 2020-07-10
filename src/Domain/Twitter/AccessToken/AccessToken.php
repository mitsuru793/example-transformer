<?php
declare(strict_types=1);

namespace Php\Domain\Twitter\AccessToken;

final class AccessToken
{
    public ?int $id;

    public string $token;

    public string $secret;

    public int $twitterUserId;

    public string $screenName;

    public function __construct(?int $id, string $token, string $secret, int $twitterUserId, string $screenName)
    {
        $this->id = $id;
        $this->token = $token;
        $this->secret = $secret;
        $this->twitterUserId = $twitterUserId;
        $this->screenName = $screenName;
    }

    public static function fromTwitterResponse(array $params): self
    {
        return new self(
            null,
            $params['oauth_token'], $params['oauth_token_secret'],
            (int)$params['user_id'], $params['screen_name']
        );
    }
}
