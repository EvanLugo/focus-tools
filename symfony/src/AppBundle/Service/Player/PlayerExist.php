<?php


namespace AppBundle\Service\Player;

use AppBundle\Entity\Player;
use Doctrine\ORM\EntityManagerInterface;

class PlayerExist
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(string $player): ?Player
    {
        return $this->em->getRepository(Player::class)->findOneBy(
            [
                'name' => $player
            ]
        );
    }
}
