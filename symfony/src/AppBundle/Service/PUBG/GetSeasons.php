<?php

namespace AppBundle\Service\PUBG;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GetSeasons
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function __invoke()
    {
        $response = $this->client->request(
            'GET',
            'https://api.pubg.com/shards/xbox/seasons'
        );

        $content = $response->getContent();

        return json_decode($content, true);
    }
}
