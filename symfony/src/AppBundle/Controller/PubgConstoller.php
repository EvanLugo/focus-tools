<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Player;
use AppBundle\Form\PlayerType;
use AppBundle\Service\PUBG\GetPlayers;
use AppBundle\Service\PUBG\GetPlayersStats;
use AppBundle\Service\PUBG\GetSeasons;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PubgConstoller extends AbstractController
{
    /**
     * @Route("/browser", name="browser")
     */
    public function playersBrowserAction(Request $request, GetPlayers $playersService, GetPlayersStats $statsService)
    {
        $form = $this->createFormBuilder()
            ->add('players', TextType::class)
            ->add('search', SubmitType::class, [
                'label' => 'Search Players'
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $playersData = $playersService->__invoke($data['players']);
            $playerObjects = [];
            $forms = [];
            foreach ($playersData['data'] as $player) {
                $playerObject = new Player();
                $playerObject->setAccount($player['id']);
                $playerObject->setName($player['attributes']['name']);
                $playerObject->setPlatform($player['attributes']['shardId']);

                $rankedStats = $statsService->__invoke($playerObject->getAccount());

                $playerObject->setRankedTier(
                    sprintf(
                        '%s-%s',
                        $rankedStats['data']['attributes']['rankedGameModeStats']['squad']['currentTier']['tier'],
                        $rankedStats['data']['attributes']['rankedGameModeStats']['squad']['currentTier']['subTier']
                    )
                );
                $playerObject->setKda(
                    (int) $rankedStats['data']['attributes']['rankedGameModeStats']['squad']['kda']
                );

                $playerObjects[] = $playerObject;
                $form = $this->createForm(PlayerType::class, $playerObject);
                $forms[] = $form->createView();
            }

            return $this->render('pubg/playersList.html.twig',
                [
                    'players' => $playerObjects,
                    'forms' => $forms
                ]);
        }

        return $this->render('pubg/browser.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/seasons", name="season")
     *
     * @param GetSeasons $seasonService
     */
    public function seasonsAction(GetSeasons $seasonService)
    {
        $seasons = $seasonService->__invoke();
        dd($seasons);
    }
}
