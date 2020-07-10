<?php
declare(strict_types=1);

namespace Php\Application\Actions\Twitter;

use Abraham\TwitterOAuth\TwitterOAuth;
use League\Plates\Engine;
use Php\Application\ActionError;
use Php\Application\ActionPayload;
use Psr\Http\Message\ResponseInterface as Response;

final class LoginTwitterAction extends TwitterAction
{
    private TwitterOAuth $twitter;

    public function __construct(Engine $templates, TwitterOAuth $twitter)
    {
        parent::__construct($templates);
        $this->twitter = $twitter;
    }

    protected function action(): Response
    {
        $requestToken = $this->twitter->oauth('oauth/request_token', [
            // 'oauth_callback' => Origin::web() . '/twitter_callback'
            // Twitterに登録したcallback urlと違えば下記のエラーが出るが、パラメータを送信しなくてもcallbackにリダイレクトされる。
            // 405 Callback URL not approved for this client application. Approved callback URLs can be adjusted in your application settings.
        ]);
        if (!$token = $requestToken['oauth_token'] ?? null) {
            $err = new ActionError(ActionError::SERVER_ERROR, 'Not found oauth_token in response from oauth/request_token of twitter.');
            return $this->respond(new ActionPayload(500, null, $err));
        }
        $this->response = $this->setOauthToken($this->response, $token);

        $url = $this->twitter->url('oauth/authorize', ['oauth_token' => $requestToken['oauth_token']]);
        $html = <<<HTML
        <a href="$url">Sign in with Twitter</a>
        HTML;

        $this->renderView($this->response,
            'debug/render-html', compact('html') + ['loginUser' => $this->loginUser]
        );
        return $this->response;
    }
}
