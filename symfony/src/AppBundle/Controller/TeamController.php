<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Team;
use AppBundle\Form\TeamType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class TeamController extends AbstractController
{
    /**
     * @Route("/teams/form", name="team_form")
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

            return $this->redirectToRoute('team_list');
        }

        return $this->render(
            'teams/teamForm.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/teams/list", name="team_list")
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
}
