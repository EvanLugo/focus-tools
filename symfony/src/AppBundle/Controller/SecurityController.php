<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Player;
use AppBundle\Entity\User;
use AppBundle\Form\UserType;
use AppBundle\Service\Player\CreatePlayer;
use AppBundle\Service\Player\PlayerExist;
use AppBundle\Service\PUBG\GetPlayers;
use AppBundle\Service\PUBG\GetPlayersStats;
use AppBundle\Service\User\UserPlayerExist;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Exception;
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
    public function registerAction(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordEncoderInterface $encoder,
        GetPlayers $getPlayer,
        GetPlayersStats $statsService,
        CreatePlayer $createPlayer,
        PlayerExist $playerExist,
        UserPlayerExist $userPlayerExist
    ) {
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $form->getData();

            $playerCreated = $playerExist->__invoke($user['player']);
            if ($playerCreated !== null) {
                $userObject = $userPlayerExist->__invoke($playerCreated);

                if ($userObject !== null) {
                    $this->addFlash('danger', sprintf('There is an user with gamer tag [%s] already', $playerCreated->getName()));
                    return $this->redirectToRoute('register');
                }
            } else {
                try {
                    $playerData = $getPlayer->__invoke($user['player'], $user['xbox'], $user['psn']);
                } catch (Exception $e) {
                    $msg = sprintf('There was an error with PUBG API, try it again. code[%s]', $e->getCode());
                    if ($e->getCode() === 404) {
                        $msg = sprintf('The player: [%s] was not found', $user['player']);
                    }

                    $this->addFlash('warning', $msg);
                    return $this->redirectToRoute('register');
                }

                foreach ($playerData as $player) {
                    $rankedStats = $statsService->__invoke($player['id']);
                    $rankedTier = sprintf(
                        '%s-%s',
                        $rankedStats['data']['attributes']['rankedGameModeStats']['squad']['currentTier']['tier'],
                        $rankedStats['data']['attributes']['rankedGameModeStats']['squad']['currentTier']['subTier']
                    );

                    /** @var Player $playerCreated */
                    $playerCreated = $createPlayer->__invoke(
                        $player['id'],
                        $player['attributes']['name'],
                        $player['attributes']['shardId'],
                        (float) $rankedStats['data']['attributes']['rankedGameModeStats']['squad']['kda'],
                        $rankedTier,
                        0,
                        0
                    );
                }
            }

            $userObject = new User();
            $userObject->setName($user['name']);
            $userObject->setUsername($user['username']);
            $userObject->setRoles(['ROLE_USER']);
            $userObject->setPassword($encoder->encodePassword($userObject, $user['password']));
            $userObject->setPlayer($playerCreated);
            $em->persist($userObject);

            try {
                $em->flush();
            } catch (UniqueConstraintViolationException $e) {
                $this->addFlash('danger', sprintf('username: %s already exists', $userObject->getUsername()));
                return $this->redirectToRoute('register');
            }


            $this->addFlash(
                'success',
                sprintf(
                    'User %s registered successfully',
                    $userObject->getName()
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
