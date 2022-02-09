<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Player;
use AppBundle\Service\GetPlayer;
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
    public function playersBrowserAction(Request $request, GetPlayer $playersService)
    {
        $form = $this->createFormBuilder()
            ->add('players', TextType::class)
            ->add('search', SubmitType::class, [
                'label' => 'Search Player ex: player,player2'
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $playersData = $playersService->__invoke($data['players']);
            $playerObjects = [];

            foreach ($playersData['data'] as $player) {
                $playerObject = new Player();
                $playerObject->setAccount($player['id']);
                $playerObject->setName($player['attributes']['name']);
                $playerObject->setPlatform($player['attributes']['shardId']);

                $playerObjects[] = $playerObject;
            }

            return $this->render('pubg/playersList.html.twig',
                [
                    'players' => $playerObjects
                ]);
        }

        return $this->render('pubg/browser.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }
}
