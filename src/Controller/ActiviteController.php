<?php

namespace App\Controller;

use App\Entity\Activite;
use App\Form\ActiviteType;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Snappy\Pdf;
use Ob\HighchartsBundle\Highcharts\Highchart; 

#[Route('/activite')]
class ActiviteController extends AbstractController
{



    #[Route('/', name: 'app_activite_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {
        $query = $entityManager->getRepository(Activite::class)->createQueryBuilder('a')->getQuery();

        $activites = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            10
        );

        return $this->render('activite/index.html.twig', [
            'activites' => $activites,
        ]);
    }

    #[Route('/new', name: 'app_activite_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $activite = new Activite();
        $form = $this->createForm(ActiviteType::class, $activite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($activite);
            $entityManager->flush();

            $recipient = $activite->getEmaildist();
            $this->mailService->sendActivationEmail($recipient, 'Nouvelle activité enregistrée', 'Une nouvelle activité a été enregistrée.');

            return $this->redirectToRoute('app_activite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('activite/new.html.twig', [
            'activite' => $activite,
            'form' => $form,
        ]);
    }

    #[Route('/{idact}', name: 'app_activite_show', methods: ['GET'])]
    public function show(Activite $activite = null): Response
    {
        if ($activite === null) {
            return $this->redirectToRoute('app_activite_index');
        }

        return $this->render('activite/show.html.twig', [
            'activite' => $activite,
        ]);
    }

    #[Route('/{idact}/edit', name: 'app_activite_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Activite $activite, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ActiviteType::class, $activite);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_activite_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('activite/edit.html.twig', [
            'activite' => $activite,
            'form' => $form,
        ]);
    }

    #[Route('/{idact}', name: 'app_activite_delete', methods: ['POST'])]
    public function delete(Request $request, Activite $activite, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $activite->getIdact(), $request->request->get('_token'))) {
            $entityManager->remove($activite);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_activite_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/export-pdf', name: 'app_activite_export_pdf', methods: ['GET'])]
    public function exportPdf(EntityManagerInterface $entityManager, Pdf $pdf): Response
    {
        try {
            $activites = $entityManager->getRepository(Activite::class)->findAll();

            if (empty($activites)) {
                throw $this->createNotFoundException('Aucune activité trouvée');
            }

            $html = $this->renderView('activite/pdf.html.twig', [
                'activites' => $activites,
            ]);

            $filename = 'activites.pdf';

            return new Response(
                $pdf->getOutputFromHtml($html),
                200,
                [
                    'Content-Type' => 'application/pdf',
                    'Content-Disposition' => 'attachment; filename="' . $filename . '"',
                ]
            );
        } catch (\Exception $e) {
            dump($e->getMessage());
            return new Response('Erreur lors de la génération du PDF', 500);
        }
    }

    #[Route('/statistics', name: 'app_activite_statistics', methods: ['GET'])]
    public function statistics(EntityManagerInterface $entityManager): Response
    {
        $activites = $entityManager->getRepository(Activite::class)->findAll();

        $enAttenteCount = 0;
        $termineCount = 0;

        foreach ($activites as $activite) {
            if ($activite->getEtatact() === 'en attente') {
                $enAttenteCount++;
            } elseif ($activite->getEtatact() === 'terminé') {
                $termineCount++;
            }
        }

        $chart = new Highchart();

        $chart->chart->renderTo('chart');
        $chart->title->text('Statistiques des activités');
        $chart->plotOptions->pie([
            'allowPointSelect' => true,
            'cursor' => 'pointer',
            'dataLabels' => [
                'enabled' => true,
                'format' => '<b>{point.name}</b>: {point.percentage:.1f} %',
            ],
        ]);

        $chart->series[] = [
            'type' => 'pie',
            'name' => 'Statistiques',
            'data' => [
                ['En Attente', $enAttenteCount],
                ['Terminé', $termineCount],
            ],
        ];

        return $this->render('activite/statistics.html.twig', [
            'chart' => $chart,
        ]);
    }

    #[Route('/show-statistics', name: 'app_activite_show_statistics', methods: ['GET'])]
    public function showStatistics(): Response
    {
        return $this->redirectToRoute('app_activite_statistics');
    }
}
