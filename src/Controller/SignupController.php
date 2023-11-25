<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\File\Exception\FileException; // Ajout de l'import manquant
use App\Entity\Users;
use App\Service\QrCodeGenerator;
use App\Form\Users1Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;





use App\Service\FacialRecognitionService;


class SignupController extends AbstractController
{
    private $facialRecognitionService;

    public function __construct(EntityManagerInterface $entityManager, FacialRecognitionService $facialRecognitionService)
    {
        // ...
        $this->facialRecognitionService = $facialRecognitionService;
    }

    #[Route('/signup', name: 'app_signup')]
    public function signup(Request $request, EntityManagerInterface $entityManager, SessionInterface $session): Response
    {
        $user = new Users();
    
        // Création du formulaire
        $form = $this->createForm(Users1Type::class, $user);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            // Récupération du fichier image
            /** @var UploadedFile $profileImageFile */
            $profileImageFile = $user->getProfileImageFile();
    
            // Vérifiez si un fichier a été téléchargé
            if ($profileImageFile) {
                // Générez un nom unique pour le fichier avant de l'enregistrer
                $newFilename = md5(uniqid()).'.'.$profileImageFile->guessExtension();
    
                // Déplacez le fichier dans le répertoire où sont stockées les images
                try {
                    $profileImageFile->move(
                        $this->getParameter('profile_images_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Gestion des erreurs de téléchargement
                    $this->addFlash('error', 'Une erreur s\'est produite lors du téléchargement de l\'image.');
                    return $this->redirectToRoute('app_signup');
                }
    
                // Enregistrez le chemin de l'image dans l'entité User
                $user->setProfileImage($newFilename);
    
                // Sauvegardez également le chemin du fichier dans la session
                $this->get('session')->set('profile_image_path', $this->getParameter('profile_images_directory').'/'.$newFilename);
            }
    
            // Enregistrez l'utilisateur dans la base de données
            $entityManager->persist($user);
            $entityManager->flush();
    
            // Ajoutez l'ID de l'utilisateur à la session
            $session->set('user_id', $user->getId());
    
            $this->addFlash('success', 'Inscription réussie ! Vous pouvez maintenant vous connecter.');
            return $this->redirectToRoute('show_qr_code');
        }
    
        return $this->render('auth/signup.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    

    #[Route('/show-qr-code', name: 'show_qr_code')]
    public function showQRCode(QrCodeGenerator $qrCodeGenerator, SessionInterface $session, LoggerInterface $logger): Response
    {
        // Récupérez l'ID de l'utilisateur depuis la session
        $userId = $session->get('user_id');
    
        // Assurez-vous que l'ID de l'utilisateur est disponible
        if (!$userId || !is_int($userId)) {
            throw new \RuntimeException('User ID not found in session.');
        }
    
        // Récupérez l'objet User complet depuis la base de données
        $user = $this->getDoctrine()->getRepository(Users::class)->find($userId);
    
        // Vérifiez si l'utilisateur existe
        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé.');
        }
    
        // Récupérez le chemin du fichier depuis la session
        $profileImagePath = $session->get('profile_image_path');
    
        // Assurez-vous que le chemin du fichier est disponible
        if (!$profileImagePath || !is_string($profileImagePath)) {
            throw new \RuntimeException('Profile image path not found in session.');
        }
    
        // Ajoutez des informations de journalisation pour déboguer
        $logger->info('Attempting to show QR Code.');
    
        // Vérifiez si l'utilisateur est connecté
        if ($user) {
            // Ajoutez des informations de journalisation pour déboguer
            $logger->info('User data retrieved from the database.');
    
            // Assurez-vous que les méthodes getMail() et getMotdepasse() existent et ne sont pas nulles
            if (method_exists($user, 'getMail') && method_exists($user, 'getMotdepasse')) {
                $email = $user->getMail();
                $password = $user->getMotdepasse();
    
                // Ajoutez des informations de journalisation pour déboguer
                $logger->info('User data retrieved successfully.');
    
                // Générez le QR code
                $qrCode = $qrCodeGenerator->generateQRCode($email, $password);
    
                // Ajoutez des informations de journalisation pour déboguer
                $logger->info('QR Code generated successfully.');
    
                // Retournez la vue avec le QR code
                return $this->render('auth/show_qr_code.html.twig', [
                    'qrCode' => $qrCode,
                ]);
            } else {
                // Gestion de l'absence des méthodes nécessaires
                // Ajoutez des informations de journalisation pour déboguer
                $logger->error('Error: Missing required methods in user data.');
    
                // Redirigez l'utilisateur vers une page d'erreur ou effectuez une autre action appropriée
                // ...
    
                // Vous pouvez également ajouter un message flash pour informer l'utilisateur
                $this->addFlash('error', 'Erreur lors de la récupération des données du client.');
            }
        } else {
            // L'utilisateur n'est pas connecté, redirigez-le vers la page de connexion
            // Ajoutez des informations de journalisation pour déboguer
            $logger->warning('User is not authenticated, redirecting to login page.');
            return $this->redirectToRoute('app_signin');
        }
    }
    

}
