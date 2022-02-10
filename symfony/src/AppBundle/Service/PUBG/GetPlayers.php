<?php

namespace AppBundle\Service\PUBG;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GetPlayers
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function __invoke(string $players)
    {
        $response = $this->client->request(
            'GET',
            'https://api.pubg.com/shards/xbox/players',
            [
                'query' => [
                    'filter[playerNames]' => $players
                ]
            ]
        );

        $content = $response->getContent();

        return json_decode($content, true);
    }
}
