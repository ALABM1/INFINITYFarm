<?php

namespace App\Controller;

use App\Entity\User;
use App\Service\SmsService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;



class ResetSmsController extends AbstractController
{
    private $entityManager;
    private $smsService;

    public function __construct(EntityManagerInterface $entityManager, SmsService $smsService)
    {
        $this->entityManager = $entityManager;
        $this->smsService = $smsService;
    }

    #[Route('/reset-sms', name: 'reset_sms')]
    public function resetSms(Request $request): Response
    {
        return $this->render('send_sms/index.html.twig');
    }

    #[Route('/send-sms', name: 'send_sms', methods: ['POST'])]
    public function sendSms(Request $request): Response
    {
        $phoneNumber = $request->request->get('phoneNumber');
        $userRepository = $this->getDoctrine()->getRepository(User::class);
        $user = $userRepository->findOneBy(['numerotelephone' => $phoneNumber]);

        if (!$user) {
            $this->addFlash('error', 'Numéro de téléphone non enregistré.');
            return $this->redirectToRoute('reset_sms');
        }

        $verificationCode = str_pad(mt_rand(0, 999999), 6, '0', STR_PAD_LEFT);

        $sql = "INSERT INTO reset_password2 (phone, reset_code2, created_at) VALUES (?, ?, NOW())";
        $this->entityManager->getConnection()->executeStatement($sql, [$phoneNumber, $verificationCode]);

        $phoneNumberWithCountryCode = '+216' . $phoneNumber;
        $this->smsService->sendSms($phoneNumberWithCountryCode, 'Votre code de réinitialisation est : ' . $verificationCode);

        $this->addFlash('success', 'Code de vérification envoyé avec succès par SMS!');

        return $this->redirectToRoute('verify_sms', ['phone_number' => $phoneNumber]);
    }

    #[Route('/verify-sms/{phone_number}', name: 'verify_sms')]
    public function verifySms(Request $request, string $phone_number): Response
    {
        return $this->render('send_sms/verify_sms.html.twig', [
            'phone_number' => $phone_number,
        ]);
    }

    #[Route('/verification-sms', name: 'verification_sms', methods: ['POST'])]
    public function processVerification(Request $request): Response
    {
        $phone = $request->request->get('phone_number');
        $codeFromUser = $request->request->get('verification_code');

        $codeFromDatabase = $this->entityManager->getConnection()->fetchOne(
            "SELECT reset_code2 FROM reset_password2 WHERE phone = ? ORDER BY id DESC LIMIT 1",
            [$phone]
        );

        if (strcasecmp($codeFromUser, $codeFromDatabase) === 0) {
            // Requête SQL pour récupérer l'email associé au numéro de téléphone
            $email = $this->entityManager->getConnection()->fetchOne(
                "SELECT mail FROM user WHERE numeroTelephone = ?",
                [$phone]
            );

            return $this->redirectToRoute('reset_password', ['email' => $email]);
        } else {
            return $this->render('send_sms/failure.html.twig');
        }
    }




}
