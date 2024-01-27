<?php


namespace App\Controller;

use App\Repository\MaterielRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


#[Route('/mail')]
class MailController extends AbstractController
{
    private MaterielRepository $materielRepository;
    private MailerInterface $mailer;

    #[Route('/send_email', name: 'send_panne_email')]
    public function sendPanneEmail(MailerInterface $mailer, MaterielRepository $materielRepository ,  EntityManagerInterface $entityManager): Response
    {
        // Récupérer les matériels en panne
        $materielsEnPanne = $materielRepository->findMaterielsEnPanne();

        // Vérifier s'il y a des matériels en panne
        if (count($materielsEnPanne) > 0) {
            // Construire le contenu de l'e-mail au format HTML
            $htmlContent = $this->renderView('mail/liste_panne.html.twig', ['materiels' => $materielsEnPanne]);

            // Créer l'e-mail
            $email = (new Email())
                ->from('houssemmeguebli@outlook.com')
                ->to('megbli.houssam@gmail.com')
                ->subject('Liste des matériels en panne')
                ->html($htmlContent);

            try {
                // Envoyer l'e-mail
                $mailer->send($email);
                $this->addFlash('success', 'E-mail envoyé avec succès !');
            } catch (TransportExceptionInterface $e) {
                $this->addFlash('error', 'Erreur lors de l\'envoi de l\'e-mail : ' . $e->getMessage());
            }
        } else {
            $this->addFlash('info', 'Aucun matériel en panne à signaler.');
        }

        return $this->render('mail/liste_panne.html.twig', ['materiels' => $materielsEnPanne]);
    }


}

