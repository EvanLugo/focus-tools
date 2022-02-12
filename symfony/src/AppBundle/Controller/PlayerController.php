<?php

namespace AppBundle\Controller;

use AppBundle\Service\Player\CreatePlayer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PlayerController extends AbstractController
{
    /**
     * @Route("/savePlayer", name="savePlayer")
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
            (int) $post['team']
        );

        return new JsonResponse($player);
    }
}
