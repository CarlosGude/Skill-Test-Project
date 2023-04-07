<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             return $this->redirectToRoute('app_base');
        }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/activate/user/{token}', name: 'app_activate_user')]
    public function index(string $token, EntityManagerInterface $manager): Response
    {
        /** @var User $user */
        $user = $manager->getRepository(User::class)->findOneBy(['token' => $token]);

        if(!$user){
            //TODO Add alert
            return $this->redirectToRoute('app_base');
        }

        $user->setActive(true);
        $user->setToken();

        $manager->flush();

        //TODO Add alert
        return $this->redirectToRoute('app_base');

    }
}
