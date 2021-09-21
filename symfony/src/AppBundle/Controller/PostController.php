<?php


namespace AppBundle\Controller;


use AppBundle\Entity\Post;
use AppBundle\Form\PostFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends Controller
{
    /**
     * @Route("/", name="post_index")
     */
    public function indexAction(Request $request, EntityManagerInterface $em)
    {
        /** @var  $from */
        $form = $this->createForm(PostFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            /** @var Post $post */
            $post = $form->getData();

            /** @var UploadedFile $path */
            $path = $form->get('path')->getData();
            /** @var UploadedFile $image */
            $image = $form->get('image')->getData();

            $extension = 'jpeg';

            $pathName = sprintf('%s_%s.%s', $path->getFilename(), rand(1, 99999), $extension);
            $imageName = sprintf('%s_%s.%s', $image->getFilename(), rand(1, 99999), $extension);

            $path->move($this->getParameter('posts_paths'), $pathName);
            $image->move($this->getParameter('posts_images'), $imageName);

            $post->setPath($pathName);
            $post->setImage($imageName);

            $em->persist($post);
            $em->flush();

            return $this->redirectToRoute('post_list');
        }

        return $this->render(
            'post/index.html.twig',
            [
                'postForm' => $form->createView()
            ]);
    }

    /**
     * @Route("/post/list", name="post_list")
     */
    public function postListAction(EntityManagerInterface $em)
    {
        $posts = $em->getRepository(Post::class)->findAll();

        return $this->render(
            'post/list.html.twig',
            [
                'posts' => $posts
            ]
        );
    }
}
