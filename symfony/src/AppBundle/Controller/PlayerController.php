<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Player;
use AppBundle\Service\Player\CreatePlayer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PlayerController extends AbstractController
{
    /**
     * @Route("app/savePlayer", name="savePlayer")
     */
    public function savePlayerAction(Request $request, CreatePlayer $playerService)
    {
        $post = $request->query->all();

        $player = $playerService->__invoke(
            $post['account'],
            $post['name'],
            $post['platform'],
            (float) $post['kda'],
            $post['rankedTier'],
            (int) $post['team'],
            ($post['captain'] === 'true') ? 1 : 0
        );

        return new JsonResponse($player);
    }

    /**
     * @Route("app/playersList", name="players_list")
     */
    public function playersListAction(EntityManagerInterface $em)
    {
        $players =  $em->getRepository(Player::class)->findAll();

        return $this->render(
            'players/list.html.twig',
            [
                'players' => $players
            ]
        );
    }
}
