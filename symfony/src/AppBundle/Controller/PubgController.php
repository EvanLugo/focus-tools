<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Player;
use AppBundle\Service\Player\PlayerExist;
use AppBundle\Service\PUBG\GetMatches;
use AppBundle\Service\PUBG\GetPlayers;
use AppBundle\Service\PUBG\GetPlayersStats;
use AppBundle\Service\PUBG\GetSeasons;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PubgController extends AbstractController
{
    /**
     * @Route("app/team/players", name="form_team_players")
     */
    public function playersBrowserAction(
        Request $request,
        GetPlayers $playersService,
        GetPlayersStats $statsService,
        PlayerExist $playerExist
    ) {
        $form = $this->createFormBuilder()
            ->add('players', TextType::class)
            ->add('xbox', CheckboxType::class, [
                'required' => false,
                'label' => false,
                'label_attr' => ['class' => 'fab fa-xbox']
            ])
            ->add('psn', CheckboxType::class, [
                'required' => false,
                'label' => false,
                'label_attr' => ['class' => 'fab fa-playstation']
            ])
            ->add('search', SubmitType::class, [
                'label' => 'Search Players',
                'attr' => [
                    'class' => 'btn btn-dark'
                ]
            ])
            ->getForm();

        $idTeam = $request->query->get('idTeam' );

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $playersAlreadyCreated = [];
            $playersNames = explode(',', $data['players']);

            foreach ($playersNames as $i => $playerName) {
                $playerCreated = $playerExist->__invoke($playerName);

                if ($playerCreated !== null) {
                    $playersAlreadyCreated[] = $playerCreated;
                    unset($playersNames[$i]);

                    continue;
                }
            }

            $playersData = [];
            if (count($playersNames) >= 1) {
                try {
                    $playersNames = implode(',', $playersNames);
                    $playersData = $playersService->__invoke($playersNames, $data['xbox'], $data['psn']);
                } catch (Exception $e) {
                    $msg = sprintf('There was an error with PUBG API, try it again. code[%s]', $e->getCode());
                    if ($e->getCode() === 404) {
                        $msg = sprintf('The players: [%s] were not found', $data['players']);
                    }

                    $this->addFlash('warning', $msg);

                    return $this->redirectToRoute('form_team_players', ['idTeam' => $idTeam]);
                }
            }

            $playerObjects = [];

            foreach ($playersData as $player) {
                $playerObject = new Player();
                $playerObject->setAccount($player['id']);
                $playerObject->setName($player['attributes']['name']);
                $playerObject->setPlatform($player['attributes']['shardId']);
                $playerObject->setIdTeam($idTeam);

                $rankedStats = $statsService->__invoke($playerObject->getAccount());

                $playerObject->setRankedTier(
                    sprintf(
                        '%s-%s',
                        $rankedStats['data']['attributes']['rankedGameModeStats']['squad']['currentTier']['tier'],
                        $rankedStats['data']['attributes']['rankedGameModeStats']['squad']['currentTier']['subTier']
                    )
                );
                $playerObject->setKda(
                    (float) $rankedStats['data']['attributes']['rankedGameModeStats']['squad']['kda']
                );

                $playerObjects[] = $playerObject;
            }

            $playerObjects = array_merge($playerObjects, $playersAlreadyCreated);

            if (count($playerObjects) > 0) {
                $msg = 'The all players were found !';
                $type = 'success';
                if (count($playerObjects) < count(explode(',', $data['players']))) {
                    $msg = 'The next players were found, seems that some players were not found, make sure to type the names correctly !';
                    $type = 'warning';
                }

                $this->addFlash($type, $msg);
            } else {
                $this->addFlash('danger', 'Any of these player names were found, make sure to type the names correctly.');
            }


            return $this->render('pubg/playersList.html.twig', [
                    'players' => $playerObjects,
                    'idTeam' => $idTeam
                ]);
        }

        return $this->render('pubg/browser.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("app/seasons", name="season")
     *
     * @param GetSeasons $seasonService
     */
    public function seasonsAction(GetSeasons $seasonService)
    {
        $seasons = $seasonService->__invoke();
        dd($seasons);
    }

    /**
     * @Route("app/matchesBrowser", name="matches_browser")
     */
    public function matchesBrowserAction(Request $request, GetMatches $getMatches)
    {
        $form = $this->createFormBuilder()
            ->add('player', TextType::class)
            ->add('xbox', CheckboxType::class, [
                'attr' => [
                    'class' => 'platform'
                ],
                'required' => false,
                'label' => false,
                'label_attr' => ['class' => 'fab fa-xbox']
            ])
            ->add('psn', CheckboxType::class, [
                'attr' => [
                    'class' => 'platform'
                ],
                'required' => false,
                'label' => false,
                'label_attr' => ['class' => 'fab fa-playstation']
            ])
            ->add('matches', ChoiceType::class, [
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5,
                    '6' => 6
                ]
            ])
            ->add('search matches', SubmitType::class, [
                'label' => 'Search Matches',
                'attr' => [
                    'class' => 'btn btn-dark'
                ]
            ])
            ->getForm();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $matchesData = $getMatches->__invoke($data['player'], $data['xbox'], $data['psn'], $data['matches']);

            return $this->render(
                'matches/browser.html.twig',
                [
                    'form' => $form->createView(),
                    'matches' => $matchesData
                ]
            );
        }

        return $this->render(
            'matches/browser.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/testApi", name="test_api")
     */
    public function testApiAction(GetPlayers $getPlayers)
    {
        $response = $getPlayers->__invoke('LPG Levi', true, false);
        dd($response);
    }
}
