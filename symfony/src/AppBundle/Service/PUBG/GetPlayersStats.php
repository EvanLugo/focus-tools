<?php

namespace AppBundle\Service\PUBG;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GetPlayersStats
{
    private $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    public function __invoke(string $playerAccount)
    {
        $url = sprintf(
            'https://api.pubg.com/shards/xbox/players/%s/seasons/division.bro.official.console-15/ranked',
            $playerAccount
        );
        $response = $this->client->request(
            'GET',
            $url
        );

        $content = $response->getContent();

        return json_decode($content, true);
    }
}
