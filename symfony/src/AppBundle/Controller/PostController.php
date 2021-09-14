<?php


namespace AppBundle\Controller;


use AppBundle\Form\PostFormType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends Controller
{
    /**
     * @Route("/", name="post_index")
     */
    public function indexAction(Request $request)
    {
        /** @var  $from */
        $form = $this->createForm(PostFormType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

        }

        return $this->render(
            'post/index.html.twig',
            [
                'postForm' => $form->createView()
            ]);
    }
}
