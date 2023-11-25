<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use App\Entity\Users;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;





class MailController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/mail', name: 'app_mail')]
    public function index(): Response
    {
        return $this->render('send_mail/index.html.twig', [
            'controller_name' => 'MailController',
        ]);
    }

    #[Route('/send-email', name: 'send_email')]
    public function sendEmail(Request $request, MailerInterface $mailer): Response
    {
        // Récupérer l'e-mail à partir de la requête
        $emailAddress = $request->request->get('email');

        // Vérifier si l'e-mail existe dans le tableau users
        $user = $this->entityManager->getRepository(Users::class)->findOneBy(['mail' => $emailAddress]);

        if (!$user) {
            $this->addFlash('error', 'Adresse e-mail non trouvée dans notre système.');
            return $this->redirectToRoute('app_mail'); // Rediriger vers la page d'envoi d'e-mail
        }

        // Générer un code aléatoire de 6 chiffres
        $verificationCode = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

        // Enregistrer le code dans le tableau reset_password
        $sql = "INSERT INTO reset_password (email, reset_code) VALUES (?, ?)";
        $this->entityManager->getConnection()->executeStatement($sql, [$emailAddress, $verificationCode]);

        // Votre logique d'envoi d'e-mail ici
        $email = (new Email())
            ->from('houssemmeguebli@outlook.com')
            ->to($emailAddress)
            ->subject('Votre Code de Vérification')
            ->text('Votre code de vérification est : ' . $verificationCode);

        try {
            $mailer->send($email);
            $this->addFlash('success', 'Code de vérification envoyé avec succès à votre adresse e-mail!');
        } catch (TransportExceptionInterface $e) {
            $this->addFlash('error', 'Erreur lors de l\'envoi de l\'e-mail : ' . $e->getMessage());
        }

        return $this->redirectToRoute('verify_code', ['email' => $emailAddress]);
    }


    #[Route('/verify-code/{email}', name: 'verify_code')]
    public function verifyCode(Request $request, string $email)
    {
        return $this->render('send_mail/verify_code.html.twig', [
            'email' => $email,
        ]);
    }

    #[Route('/process-verification', name: 'process_verification', methods: ['POST'])]
    public function processVerification(Request $request): Response
    {
    $email = $request->request->get('email');
    $codeFromUser = $request->request->get('verification_code');

    // Obtenir le dernier code de vérification de la base de données
    $sql = "SELECT reset_code FROM reset_password WHERE email = ? ORDER BY id DESC LIMIT 1";
    $codeFromDatabase = $this->entityManager->getConnection()->fetchOne($sql, [$email]);

    if ($codeFromUser === $codeFromDatabase) {
        // Code de vérification correct
        return new RedirectResponse($this->generateUrl('reset_password', ['email' => $email]));
    } else {
        // Code de vérification incorrect
        return $this->render('send_mail/failure.html.twig');
    }
}


#[Route('/reset-password/{email}', name: 'reset_password')]
    public function resetPassword(Request $request, string $email): Response
    {
        // Affichez le formulaire de réinitialisation du mot de passe
        return $this->render('send_mail/reset_password.html.twig', [
            'email' => $email,
        ]);
    }


 #[Route('/process-reset-password', name: 'process_reset_password', methods: ['POST'])]
    public function processResetPassword(Request $request, ValidatorInterface $validator, UserPasswordEncoderInterface $passwordEncoder): Response
{
    $email = $request->request->get('email');
    $newPassword = $request->request->get('new_password');

    $errors = [];

    // Valider le mot de passe
    $constraints = new Assert\Collection([
        'new_password' => [
            new Assert\NotBlank(['message' => 'Le mot de passe ne peut pas être vide.']),
            new Assert\Length(['min' => 8, 'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.']),
            new Assert\Regex([
                'pattern' => '/^(?=.*[A-Z])(?=.*\d).+$/',
                'message' => 'Le mot de passe doit contenir au moins une lettre majuscule et un chiffre.',
            ]),
        ],
    ]);

    $data = ['new_password' => $newPassword];
    $violations = $validator->validate($data, $constraints);

    foreach ($violations as $violation) {
        $propertyPath = $violation->getPropertyPath();
        $message = $violation->getMessage();

        // Utiliser le nom du champ comme clé dans le tableau d'erreurs
        $errors[$propertyPath] = $message;
    }

    // Si des erreurs sont présentes, afficher les messages d'erreur sur la même page
    if (!empty($errors)) {
        return $this->render('send_mail/reset_password.html.twig', [
            'errors' => $errors,
            'email' => $request->request->get('email'),
        ]);
    }


    // Mettez en œuvre la logique pour réinitialiser le mot de passe
    $user = $this->entityManager->getRepository(Users::class)->findOneBy(['mail' => $email]);

    if ($user) {
        // Encoder le nouveau mot de passe
        $encodedPassword = $passwordEncoder->encodePassword($user, $newPassword);

        // Mettre à jour le mot de passe dans l'entité
        $user->setMotDePasse($encodedPassword);

        // Mettre à jour l'utilisateur en base de données
        $this->entityManager->persist($user);
        $this->entityManager->flush();
    }

    // Rediriger l'utilisateur vers une page de confirmation ou de connexion
    return $this->redirectToRoute('app_signin');
}

}
