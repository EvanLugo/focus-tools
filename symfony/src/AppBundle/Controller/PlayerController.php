<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Player;
use AppBundle\Entity\Team;
use AppBundle\Service\Player\CreatePlayer;
use AppBundle\Service\Player\PlayerExist;
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
    public function savePlayerAction(
        Request $request,
        CreatePlayer $playerService,
        PlayerExist $playerExist,
        EntityManagerInterface $em
    ) {
        $post = $request->query->all();
        $player = $playerExist->__invoke($post['name']);

        if ($player !== null) {
            if (!empty($player->getTeam())) {
                return new JsonResponse([
                    'msg' => sprintf(
                        'Player [%s] is already in team [%s]!',
                        $player->getName(),
                        $player->getTeam()->getName()
                    )
                ]);
            }

            $team = $em->getRepository(Team::class)->find((int) $post['team']);
            $player->setTeam($team);
            $player->setCaptain((bool) $post['captain']);
            $em->flush();

            return new JsonResponse($player);
        }

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
