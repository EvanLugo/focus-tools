<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Player;
use AppBundle\Entity\Team;
use AppBundle\Form\TeamType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TeamController extends AbstractController
{
    /**
     * @Route("app/teams/form", name="team_form")
     */
    public function teamFormAction(Request $request, EntityManagerInterface $em)
    {
        $form = $this->createForm(TeamType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $logosFolder = $this->getParameter('logosDirectory');
            $file = $form['image']->getData();
            $team = $form->getData();
            $imageName = sprintf(
                '%s.%s',
                $team->getName(),
                $file->guessExtension()
            );
            $team->setImage($imageName);
            $file->move($logosFolder , $imageName);

            $em->persist($team);
            $em->flush();

            return $this->redirectToRoute('form_team_players', [
                'idTeam' => $team->getId()
            ]);
        }

        return $this->render(
            'teams/teamForm.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("app/teams/list", name="team_list")
     */
    public function teamListAction(EntityManagerInterface $em)
    {
        $teams = $em->getRepository(Team::class)->findAll();

        return $this->render(
            'teams/teamList.html.twig',
            [
                'teams' => $teams
            ]
        );
    }

    /**
     * @Route("app/team/players/{idTeam}", name="team_players")
     */
    public function teamPlayersAction(int $idTeam, EntityManagerInterface $em)
    {
        $team = $em->getRepository(Team::class)->find($idTeam);
        $players  = $em->getRepository(Player::class)->findBy([
            'idTeam' => $team->getId()
        ]);

        return $this->render(
            'teams/teamPlayers.html.twig',
            [
                'team' => $team,
                'players' => $players
            ]
        );
    }
}
