<?php

namespace App\Controller;

// src/Controller/SecurityController.php

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SecurityController extends AbstractController
{

    #[Route(path: '/ban', name: 'ban_page')]
    public function banPage(): Response
    {
        return $this->render('ban.html.twig');
    }
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils, SessionInterface $session): Response
    {
        // Get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // Last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        // Store data in the session (example: user ID)
        if ($this->getUser()) {
            $userId = $this->getUser()->getId();
            $session->set('user_id', $userId);

            // Redirect user based on role
            $targetRoute = $this->getTargetRouteForRole($this->getUser()->getRoles());
            return $this->redirectToRoute($targetRoute);
        }

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(SessionInterface $session): void
    {
        // Clear specific data from the session
        $session->remove('user_id');

        // Clear all session data (invalidate the session)
        $session->clear();

        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * Determine the target route based on user roles.
     *
     * @param array $roles
     * @return string
     */
    private function getTargetRouteForRole(array $roles): string
    {
        // Default route if the user has no roles
        $targetRoute = 'default_route';

        foreach ($roles as $role) {
            if ($role === 'ADMIN') {
                $targetRoute = 'app_admin1_index';
            } elseif ($role === 'AGRICULTEUR') {
                $targetRoute = 'agriculteur_dashboard';
            } elseif ($role === 'OUVRIER') {
                $targetRoute = 'ouvrier_dashboard';
            }
        }

        return $targetRoute;
    }
}
