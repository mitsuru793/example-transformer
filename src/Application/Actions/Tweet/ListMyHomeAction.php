<?php
declare(strict_types=1);

namespace Php\Application\Actions\Tweet;

use Abraham\TwitterOAuth\TwitterOAuth;
use League\Plates\Engine;
use Psr\Http\Message\ResponseInterface as Response;

final class ListMyHomeAction extends TweetAction
{
    private TwitterOAuth $client;

    public function __construct(Engine $templates, TwitterOAuth $client)
    {
        parent::__construct($templates);
        $this->client = $client;
    }

    protected function action(): Response
    {
        $tweets = $this->client->get('statuses/home_timeline', ['count' => 25, 'exclude_replies' => true]);
        $this->renderView($this->response, 'debug/dump', [
            'loginUser' => $this->loginUser, 'data' => compact('tweets')
        ]);
        return $this->response;
    }
}
