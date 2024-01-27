<?php

namespace App\Controller;

use App\Entity\Reclamations;
use App\Form\ReclamationsType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Knp\Component\Pager\PaginatorInterface;
use Knp\Snappy\Pdf;

#[Route('/backreclamation')]
class BackreclamationController extends AbstractController
{
    #[Route('/', name: 'app_backreclamations_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager, PaginatorInterface $paginator): Response
    {
        $queryBuilder = $entityManager->getRepository(Reclamations::class)->createQueryBuilder('r');

        $dateFrom = $request->query->get('dateFrom');
        $dateTo = $request->query->get('dateTo');
        $type = $request->query->get('type');

        if ($dateFrom) {
            $queryBuilder->andWhere('r.date >= :dateFrom')
                ->setParameter('dateFrom', $dateFrom);
        }

        if ($dateTo) {
            $queryBuilder->andWhere('r.date <= :dateTo')
                ->setParameter('dateTo', $dateTo);
        }

        if ($type) {
            $queryBuilder->andWhere('r.type LIKE :type')
                ->setParameter('type', '%' . $type . '%');
        }

        $query = $queryBuilder->getQuery();

        $pagination = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1), // page number
            10 // limit per page
        );

        return $this->render('backreclamation/index.html.twig', [
            'pagination' => $pagination
        ]);
    }


        #[Route('/generate-pdf-back', name: 'app_backgenerate_pdf', methods: ['GET'])]
        public function generatePdf(Request $request, EntityManagerInterface $entityManager, Pdf $pdf)
        {
            $reclamations = $entityManager->getRepository(Reclamations::class)->findAll();

            $html = $this->renderView('backpdf/template.html.twig', [
                'reclamations' => $reclamations,
            ]);

            $options = [
                'enable-local-file-access' => true,
                'header-html' => $this->renderView('backpdf/header.html.twig'),
                'footer-html' => $this->renderView('backpdf/footer.html.twig'),
            ];

            $pdfFile = $pdf->getOutputFromHtml($html, $options);

            $response = new Response($pdfFile);
            $response->headers->set('Content-Type', 'application/pdf');
            $response->headers->set('Content-Disposition', 'inline; filename="reclamations.pdf"');

            return $response;

        }


        #[Route('/new', name: 'app_backreclamations_new', methods: ['GET', 'POST'])]
        public function new(Request $request, EntityManagerInterface $entityManager): Response
        {
            $reclamation = new Reclamations();
            $reclamation->setDate(new \DateTime());

            $form = $this->createForm(ReclamationsType::class, $reclamation);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->persist($reclamation);
                $entityManager->flush();

                return $this->redirectToRoute('app_backreclamations_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('backreclamation/new.html.twig', [
                'reclamation' => $reclamation,
                'form' => $form,
            ]);
        }

        #[Route('/backstatistique', name: 'app_backreclamations_statistiques', methods: ['GET'])]
        public function statistiques(Request $request, EntityManagerInterface $entityManager): Response
        {
            $dateFrom = $request->query->get('dateFrom');
            $dateTo = $request->query->get('dateTo');

            // Convert string dates to DateTime objects
            $dateFrom = $dateFrom ? new \DateTime($dateFrom) : null;
            $dateTo = $dateTo ? new \DateTime($dateTo) : null;

            // Build the query with optional filters and group by type
            $queryBuilder = $entityManager->getRepository(Reclamations::class)->createQueryBuilder('r')
                ->select('r.type AS reclamationType, COUNT(r.id) AS typeCount');

            if ($dateFrom) {
                $queryBuilder->andWhere('r.date >= :dateFrom')->setParameter('dateFrom', $dateFrom);
            }
            if ($dateTo) {
                $queryBuilder->andWhere('r.date <= :dateTo')->setParameter('dateTo', $dateTo);
            }

            // Group by type and execute the query
            $queryBuilder->groupBy('r.type');
            $results = $queryBuilder->getQuery()->getResult();

            // Prepare the data for the chart
            $labels = [];
            $data = [];
            foreach ($results as $result) {
                $labels[] = $result['reclamationType']; // Assuming 'type' is a property of Reclamations
                $data[] = $result['typeCount'];
            }


            // Render the template with the statistics
            return $this->render('backreclamation/statistiques.html.twig', [
                'labels' => $labels,
                'data' => $data,
                'filters' => [
                    'dateFrom' => $dateFrom ? $dateFrom->format('Y-m-d') : '',
                    'dateTo' => $dateTo ? $dateTo->format('Y-m-d') : '',
                ],
            ]);
        }

        #[Route('/backstatistiques_advanced', name: 'app_backreclamations_statistiques_advanced', methods: ['GET'])]
        public function statistiques_advanced(Request $request, EntityManagerInterface $entityManager): Response
        {
            $dateFrom = $request->query->get('dateFrom');
            $dateTo = $request->query->get('dateTo');

            $dateFrom = $dateFrom ? new \DateTime($dateFrom) : null;
            $dateTo = $dateTo ? new \DateTime($dateTo) : null;

            $reclamationsByTypeData = $this->getChartData($entityManager, 'type', $dateFrom, $dateTo);
            $reclamationsByDateData = $this->getChartData($entityManager, 'date', $dateFrom, $dateTo);
            $reclamationsByTimeData = $this->getChartData($entityManager, 'time', $dateFrom, $dateTo);

            return $this->render('backreclamation/statistiques_advanced.html.twig', [
                'reclamationsByTypeData' => $reclamationsByTypeData,
                'reclamationsByDateData' => $reclamationsByDateData,
                'reclamationsByTimeData' => $reclamationsByTimeData,
            ]);
        }

        private function getChartData(EntityManagerInterface $entityManager, string $groupBy, ?\DateTimeInterface $dateFrom, ?\DateTimeInterface $dateTo): array
        {
            $queryBuilder = $entityManager->getRepository(Reclamations::class)->createQueryBuilder('r');

            switch ($groupBy) {
                case 'type':
                    $queryBuilder->select('r.type AS groupKey, COUNT(r.id) AS groupCount');
                    break;
                case 'date':
                    $queryBuilder->select("DATE_FORMAT(r.date, '%Y-%m-%d') AS groupKey, COUNT(r.id) AS groupCount");
                    break;
                case 'time':
                    $queryBuilder->select('HOUR(r.date) AS groupKey, COUNT(r.id) AS groupCount');
                    break;
                default:
                    throw new \InvalidArgumentException('Invalid groupBy value');
            }

            if ($dateFrom) {
                $queryBuilder->andWhere('r.date >= :dateFrom')->setParameter('dateFrom', $dateFrom);
            }
            if ($dateTo) {
                $queryBuilder->andWhere('r.date <= :dateTo')->setParameter('dateTo', $dateTo);
            }

            $queryBuilder->groupBy('groupKey');
            $results = $queryBuilder->getQuery()->getResult();

            $data = [];
            foreach ($results as $result) {
                $data[$result['groupKey']] = $result['groupCount'];
            }

            return $data;
        }



        #[Route('/{id}', name: 'app_backreclamations_show', methods: ['GET'])]
        public function show(Reclamations $reclamation): Response
        {
            return $this->render('backreclamation/show.html.twig', [
                'reclamation' => $reclamation,
            ]);
        }

        #[Route('/{id}/edit', name: 'app_backreclamations_edit', methods: ['GET', 'POST'])]
        public function edit(Request $request, Reclamations $reclamation, EntityManagerInterface $entityManager): Response
        {
            $form = $this->createForm(ReclamationsType::class, $reclamation);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                $entityManager->flush();

                return $this->redirectToRoute('app_backreclamations_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->renderForm('backreclamation/edit.html.twig', [
                'reclamation' => $reclamation,
                'form' => $form,
            ]);
        }

        #[Route('/{id}', name: 'app_backreclamations_delete', methods: ['POST'])]
        public function delete(Request $request, Reclamations $reclamation, EntityManagerInterface $entityManager): Response
        {
            if ($this->isCsrfTokenValid('delete'.$reclamation->getId(), $request->request->get('_token'))) {
                $entityManager->remove($reclamation);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_backreclamations_index', [], Response::HTTP_SEE_OTHER);

}
}