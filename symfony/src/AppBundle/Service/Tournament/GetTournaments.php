<?php

namespace AppBundle\Service\Tournament;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GetTournaments
{
    private $http;
    private $pubgApi;

    public function __construct(HttpClientInterface $httpClient, string $pubgApi)
    {
        $this->http = $httpClient;
        $this->pubgApi = $pubgApi;
    }

    public function __invoke()
    {
        $responseXbox = $this->http->request(
            'GET',
            'https://api.pubg.com/shards/xbox/matches/2a6dc384-9eb1-4ba6-a8dc-5d7118d978ec'
        );

        $xbox = json_decode($responseXbox->getContent(), true);
        dd($xbox);
    }
}
