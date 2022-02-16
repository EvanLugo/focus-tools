<?php

namespace AppBundle\Controller;

use AppBundle\Service\Tournament\GetTournaments;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class TournamentsController extends AbstractController
{
    /**
     * @Route("app/tournaments", name="app_tournaments")
     */
    public function listTournamentsAction(GetTournaments $tournaments)
    {
        $tournaments->__invoke();
    }
}
