<?php

namespace App\Command;

use App\Controller\MailController;
use App\Repository\MaterielRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SendMailCommand extends Command
{
    private $mailController;
    private $mailer;
    private $materielRepository;
    private $entityManager;

    public function __construct(MailController $mailController, MailerInterface $mailer, MaterielRepository $materielRepository, EntityManagerInterface $entityManager)
    {
        parent::__construct();

        $this->mailController = $mailController;
        $this->mailer = $mailer;
        $this->materielRepository = $materielRepository;
        $this->entityManager = $entityManager;
    }

    protected function configure()
    {
        $this
            ->setName('app:send-panne-email')
            ->setDescription('Envoyer l\'e-mail pour les matériels en panne');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        // Appelez la méthode du service avec les dépendances nécessaires
        $this->mailController->sendPanneEmail($this->mailer, $this->materielRepository, $this->entityManager);

        // Affichez un message dans la sortie
        $output->writeln('E-mail envoyé avec succès !');

        return Command::SUCCESS;
    }
}
