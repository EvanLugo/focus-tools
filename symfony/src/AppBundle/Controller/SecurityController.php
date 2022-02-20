<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/login", name="login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('default/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/register", name="register")
     */
    public function registerAction(Request $request, EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();
            $user->setRoles(['ROLE_USER']);
            $user->setPassword($encoder->encodePassword($user, $user->getPassword()));

            $em->persist($user);
            $em->flush();

            $this->addFlash(
                'success',
                sprintf(
                    'User %s registered successfully',
                    $user->getName()
                )
            );

            return $this->redirectToRoute('login');
        }

        return $this->render(
            'default/userForm.html.twig',
            [
                'form' => $form->createView()
            ]
        );
    }

    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/generate_admin", name="generate_admin")
     */
    public function createUserAction(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder)
    {
        $user = new User();
        $user->setUsername('evan.lugo.trejo@gmail.com');
        $user->setPassword($encoder->encodePassword($user, 'admin123'));
        $user->setRoles([
            'ROLE_SUPER_ADMIN'
        ]);

        $em->persist($user);
        $em->flush();
    }
}
