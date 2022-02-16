<?php

namespace AppBundle\Service\PUBG;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class GetPlayers
{
    private $client;
    private $pubgApi;

    public function __construct(HttpClientInterface $client, string $pubgApi)
    {
        $this->client = $client;
        $this->pubgApi = $pubgApi;
    }

    public function __invoke(string $players, bool $xbox, bool $psn)
    {
        $playersObjects = [];

        if ($xbox) {
            $pathXbox = sprintf('%s%s',$this->pubgApi,'xbox/players');
            $responseXbox = $this->client->request(
                'GET',
                $pathXbox,
                [
                    'query' => [
                        'filter[playerNames]' => $players
                    ]
                ]
            );

            $xbox = json_decode($responseXbox->getContent(), true);
            dd($xbox);
            $playersObjects = array_merge($playersObjects, $xbox['data']);
        }

        if ($psn) {
            $pathPsn = sprintf('%s%s',$this->pubgApi,'psn/players');
            $responsePsn = $this->client->request(
                'GET',
                $pathPsn,
                [
                    'query' => [
                        'filter[playerNames]' => $players
                    ]
                ]
            );

            $psn = json_decode($responsePsn->getContent(), true);
            $playersObjects = array_merge($playersObjects, $psn['data']);
        }

        return $playersObjects;
    }
}
