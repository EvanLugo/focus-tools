<?php


namespace AppBundle\Controller;

use AppBundle\Entity\Player;
use AppBundle\Entity\Post;
use AppBundle\Form\PlayerType;
use AppBundle\Form\PostFormType;
use AppBundle\Service\GetPlayers;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\BrowserKit\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends Controller
{
    /**
     * @Route("/", name="home")
     */
    public function homeAction()
    {
        $player = new Player();
        $player
            ->setName('LPG Levi')
            ->setAccount('123.456.21')
            ->setPlatform('xbox');
        $form = $this->createForm(PlayerType::class, $player);

        return $this->render('home/home.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/player", name="player")
     */
    public function playerAction()
    {

        $playerData = $this->get(GetPlayers::class)->__invoke();
        dd($playerData);

        return new Response($playerData);
    }
}
