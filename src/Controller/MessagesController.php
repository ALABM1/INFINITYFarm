<?php

namespace App\Controller;

use App\Entity\Messages;
use App\Form\MessagesType;
use App\Utils\MessageGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use Symfony\Component\Security\Core\Security;
use App\Service\ProfanityFilter;
use App\Repository\UsersRepositoryN;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use League\Csv\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;


#[Route('/messages')]
class MessagesController extends AbstractController
{


    #[Route('/', name: 'app_messages_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, PaginatorInterface $paginator,UsersRepositoryN $usersRepository,  Request $request): Response
    {
        $user = $usersRepository->find(46);

        $filter = $request->query->get('filter', 'received');
        
        $queryBuilder = $entityManager->getRepository(Messages::class)->createQueryBuilder('m');

        if ($filter === 'sent') {
            $queryBuilder->where('m.source = :user');
        } elseif ($filter === 'received') {
            $queryBuilder->where('m.destinataire = :user');
        }

        $queryBuilder->setParameter('user', $user);

        
        $dateFrom = $request->query->get('date_from');
        if ($dateFrom) {
            $queryBuilder->andWhere('m.date >= :dateFrom')
                         ->setParameter('dateFrom', new \DateTime($dateFrom));
        }

        $dateTo = $request->query->get('date_to');
        if ($dateTo) {
            $queryBuilder->andWhere('m.date <= :dateTo')
                         ->setParameter('dateTo', new \DateTime($dateTo));
        }


        $pagination = $paginator->paginate(
            $queryBuilder,
            $request->query->getInt('page', 1),
            5
        );

        return $this->render('messages/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    #[Route('/export-csv', name: 'app_export_csv', methods: ['GET'])]
    public function exportCsv(EntityManagerInterface $entityManager, UsersRepositoryN $usersRepository): StreamedResponse
    {
        $user = $usersRepository->find(46);
        $messages = $entityManager->getRepository(Messages::class)->findBy(['destinataire' => $user]);

        $csvWriter = Writer::createFromString('');

        $csvWriter->insertOne(['ID', 'Text', 'Source', 'Date']);

        // Add data rows
        foreach ($messages as $message) {
            $csvWriter->insertOne([
                $message->getId(),
                $message->getText(),
                $message->getSource()->getNom(),

            ]);
        }

        $response = new StreamedResponse(function () use ($csvWriter) {
            $outputStream = fopen('php://output', 'w');
            fwrite($outputStream, $csvWriter->getContent());
            fclose($outputStream);
        });

        // Set the response headers
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="export.csv"');

        return $response;
    }


    #[Route('/new', name: 'app_messages_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, UsersRepositoryN $usersRepository, ProfanityFilter $profanityFilter): Response
    {
        $user = $usersRepository->find(46);
        $message = new Messages();
        $message->setSource($user);
        $message->setDate(new \DateTime());

        $form = $this->createForm(MessagesType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $text = $form->get('text')->getData();
            $destinataire = $form->get('destinataire')->getData();

            if (empty($text) || empty($destinataire)) {
                $this->addFlash('error', 'Text and Destinataire cannot be empty.');
                return $this->redirectToRoute('app_messages_new');
            }

            // Filter profanity and set text
            $cleanText = $profanityFilter->filter($text);
            $message->setText($cleanText);

            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('app_messages_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('messages/new.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_messages_show', methods: ['GET'])]
    public function show(Messages $message): Response
    {
        return $this->render('messages/show.html.twig', [
            'message' => $message,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_messages_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Messages $message, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MessagesType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_messages_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('messages/edit.html.twig', [
            'message' => $message,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_messages_delete', methods: ['POST'])]
    public function delete(Request $request, Messages $message, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$message->getId(), $request->request->get('_token'))) {
            $entityManager->remove($message);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_messages_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/generate-message', name: 'app_generate_message_api', methods: ['POST'])]
    public function generateMessageApi(Request $request, MessageGenerator $messageGenerator, CsrfTokenManagerInterface $csrfTokenManager): JsonResponse
    {
        $csrfTokenValue = $request->request->get('_csrf_token');
        $csrfToken = new CsrfToken('message_generate', $csrfTokenValue);

        if (!$csrfTokenManager->isTokenValid($csrfToken)) {
            throw new InvalidCsrfTokenException('Invalid CSRF token.');
        }

        $messageType = $request->request->get('messageType');
        $generatedMessage = $messageGenerator->generateMessage($messageType);

        return $this->json(['message' => $generatedMessage]);
    }

}
