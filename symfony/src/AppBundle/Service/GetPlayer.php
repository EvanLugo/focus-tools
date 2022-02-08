<?php

namespace AppBundle\Service;


use Symfony\Contracts\HttpClient\HttpClientInterface;

class GetPlayer
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
            'https://api.pubg.com/shards/xbox/players',
            [
                'query' => [
                    'filter[playerNames]' => 'LPG Levi,LPG MataRata'
                ]
            ]
        );

        $content = $response->getContent();

        return json_decode($content, true);
    }
}
