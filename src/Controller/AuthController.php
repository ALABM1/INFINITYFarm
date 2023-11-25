<?php

namespace App\Controller;

use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;




use Symfony\Component\Security\Core\Exception\BadCredentialsException;

use App\Service\FacialRecognitionService;



class AuthController extends AbstractController

{
    private $entityManager;
    private $passwordEncoder;

    public function __construct(EntityManagerInterface $entityManager, UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->passwordEncoder = $passwordEncoder;
    }

    #[Route('/signin', name: 'app_signin')]
    public function signin(AuthenticationUtils $authenticationUtils, Request $request, FacialRecognitionService $facialRecognitionService): Response
    {
        // Récupérer les erreurs de connexion (le cas échéant)
        $error = $authenticationUtils->getLastAuthenticationError();

        // Récupérer le dernier email saisi par l'utilisateur
        $lastEmail = $authenticationUtils->getLastUsername();

        // Ajouter une option pour la reconnaissance faciale dans le formulaire
        $facialRecognitionOption = $request->get('facial_recognition');

        // Si l'option de reconnaissance faciale est activée
        if ($facialRecognitionOption === '1') {
            // Récupérer l'image de l'utilisateur depuis le formulaire
            $userImage = $request->files->get('user_image');

            // Appeler le service de reconnaissance faciale pour vérifier l'utilisateur
            $foundUser = $facialRecognitionService->findUserBySimilarFace($userImage);

            if ($foundUser) {
                // Si un utilisateur correspondant est trouvé, vous pouvez rediriger ou effectuer d'autres actions
                // Notez que cela peut nécessiter des ajustements en fonction de votre logique spécifique
                return $this->redirectToRoute('app_home'); // Changez cela selon vos besoins
            } else {
                // Si aucun utilisateur correspondant n'est trouvé, gérer en conséquence
                throw new BadCredentialsException('Reconnaissance faciale échouée.');
            }
        }

        // Le rendu du formulaire de connexion (login)
        return $this->render('auth/signin.html.twig', [
            'error' => $error,
            'lastEmail' => $lastEmail,
            'facialRecognitionOption' => $facialRecognitionOption,
        ]);
    }





    #[Route('/signin_check', name: 'app_signin_check')]
    public function signinCheck(Request $request, EntityManagerInterface $entityManager): Response
    {
        $email = $request->request->get('_email');
        $password = $request->request->get('_password');

        // Recherche de l'utilisateur avec l'email fourni
        $user = $entityManager->getRepository(Users::class)->findOneBy(['mail' => $email]);

        if ($user) {
           
            // Vérification du mot de passe
            if ($this->passwordEncoder->isPasswordValid($user, $password)) {
                // Redirection en fonction du rôle
                switch ($user->getRole()) {
                    case 'ADMIN':
                        return $this->redirectToRoute('app_admin_index');
                        break;
                    case 'AGRICULTEUR':
                        return $this->render('agriculteur.html.twig', [
                            'user' => $user,
                        ]); break;

                    case 'OUVRIER':
                        return $this->render('ouvrier.html.twig', [
                            'user' => $user,
                        ]);
                        break;
                    default:
                        // Redirection par défaut si le rôle n'est pas reconnu
                        return $this->redirectToRoute('app_signup');
                }
            } else {
                $this->addFlash('error', 'Mot de passe incorrect');
                return $this->redirectToRoute('app_signin');
            }
        } else {
            $this->addFlash('error', 'Email incorrect');
            return $this->redirectToRoute('app_signin');
        }
    }
    #[Route('/face', name: 'app_facial_recognition')]
    public function facialRecognitionPage(): Response
    {
        // Vous pouvez ajouter ici la logique pour la page de reconnaissance faciale
        return $this->render('auth/face_recognition.html.twig');
    }


}
