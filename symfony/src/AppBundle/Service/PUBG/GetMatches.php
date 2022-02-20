<?php

namespace AppBundle\Service\PUBG;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GetMatches
{
    private $pubgApi;
    private $getPlayers;
    private $http;

    public function __construct(string $pubgApi, HttpClientInterface $http, GetPlayers $getPlayers)
    {
        $this->http = $http;
        $this->pubgApi = $pubgApi;
        $this->getPlayers = $getPlayers;
    }

    public function __invoke(string $player, bool $xbox, bool $psn, int $matches)
    {
        $matchObjects = [];
        $playerData = $this->getPlayers->__invoke($player, $xbox, $psn);

        foreach ($playerData as $player) {
            $matchObjects = array_merge($matchObjects, $this->getMatches($player['relationships']['matches']['data'], $matches));
        }

        return $matchObjects;
    }

    private function getMatches(array $matchesData, int $matchesQuantity): array
    {
        $matchObjects = [];

        foreach ($matchesData as $match) {
            if (count($matchObjects) >= $matchesQuantity) {
                return $matchObjects;
            }

            $matchObjects[] = $this->getMatchData($match['id']);
        }

        return $matchObjects;
    }

    private function getMatchData(string $id): array
    {
        $path = sprintf('%s%s%s', $this->pubgApi, 'xbox/matches/', $id);
        $response = $this->http->request('GET', $path);
        $response = json_decode($response->getContent(), true);
        return $response;
    }
}
