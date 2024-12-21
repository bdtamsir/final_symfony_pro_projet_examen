<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    #[Route(path: '/', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            $user = $this->getUser();
            $roles = $user->getRoles();

            // Redirection en fonction du rôle
            if (in_array('ROLE_BOUTIQUIER', $roles)) {
                return $this->redirectToRoute('client.index');
            }
            if (in_array('ROLE_ADMIN', $roles)) {
                return $this->redirectToRoute('user.index');
            }
            if (in_array('ROLE_CLIENT', $roles)) {
                $client = $user->getClient();
                if ($client) {
                    return $this->redirectToRoute('dette.index', ['idClient' => $client->getId()]);
                }
            }

            // Redirection par défaut si aucun rôle spécifique trouvé
            return $this->redirectToRoute('app_login'); 
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
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }
}
