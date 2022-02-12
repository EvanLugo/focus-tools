<?php

namespace AppBundle\Service\Player;

use AppBundle\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;

class CreatePlayer
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(
        string $account,
        string $name,
        string $platform,
        float $kda,
        string $rankedTier,
        int $team
    ): Player {
        $player = new Player();
        $player->setAccount($account);
        $player->setName($name);
        $player->setPlatform($platform);
        $player->setKda($kda);
        $player->setRankedTier($rankedTier);
        $player->setTeam($team);

        $this->em->persist($player);
        $this->em->flush();

        return $player;
    }
}
