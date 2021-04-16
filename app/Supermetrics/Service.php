<?php

namespace App\Supermetrics;

use App\Posts\PostHolder;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use JsonException;

class Service
{
    private Client $client;

    private string $token;

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://api.supermetrics.com/assignment/'
        ]);

        $this->register();
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function register(): void
    {
        $response = $this->client->post('register', [
            RequestOptions::FORM_PARAMS => [
                'client_id' => $_ENV['CLIENT_ID'],
                'email' => $_ENV['EMAIL'],
                'name' => $_ENV['NAME']
            ],
        ]);

        $json = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        $this->token = $json['data']['sl_token'];
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function posts(int $page = 1): PostHolder
    {
        return new PostHolder($this->_posts($page));
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    public function postMultiplePages(?int $pages = null): PostHolder
    {
        if ($pages === null) {
            $pages = (int)$_ENV['MAX_PAGES'];
        }

        $posts = [];

        for ($i = 1; $i <= $pages; $i++) {
            $posts[] = $this->_posts($i);
        }

        return new PostHolder(array_merge(...$posts));
    }

    /**
     * @throws GuzzleException
     * @throws JsonException
     */
    protected function _posts(int $page = 1): array
    {
        $response = $this->client->get('posts', [
            RequestOptions::QUERY => [
                'page' => $page,
                'sl_token' => $this->token
            ]
        ]);

        $json = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

        return $json['data']['posts'];
    }
}
