<?php

namespace AppBundle\Service\User;

use AppBundle\Entity\Player;
use AppBundle\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserPlayerExist
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function __invoke(Player $player): ?User
    {
        return $this->em->getRepository(User::class)->findOneBy([
            'player' => $player
        ]);
    }
}
