<?php

namespace App\Controller;

use App\Entity\Materiel;
use App\Form\MaterielType;
use App\Repository\MaterielRepository;
use App\Repository\ParcRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/materiel')]
class MaterielController extends AbstractController
{
    private ParcRepository $parcRepository;


    #[Route('/', name: 'app_materiel_index', methods: ['GET'])]
    public function index(Request $request, MaterielRepository $materielRepository, PaginatorInterface $paginator): Response
    {
        $nommat = $request->query->get('nommat');
        $etatmat = $request->query->get('etatmat');
        $quantitemat = $request->query->get('quantitemat');
        $dateajout = $request->query->get('dateajout');
        $nomparc = $request->query->get('nomparc');

        $criteria = [
            'nommat' => $nommat,
            'etatmat' => $etatmat,
            'quantitemat' => $quantitemat,
            'dateajout' => $dateajout,
            'nomparc' => $nomparc,
        ];

        if (!empty($nommat) || !empty($etatmat) || !empty($quantitemat) || !empty($dateajout) || !empty($nomparc)) {
            // Si au moins un paramètre de recherche est fourni, utilisez la méthode searchMatByCriteria.
            $materiels = $materielRepository->searchMatByCriteria($criteria);
        } else {
            // Si aucun paramètre de recherche n'est fourni, récupérez tous les matériels.
            $materiels = $materielRepository->findBy([], ['nommat' => 'ASC']);
        }

        // Récupérer les événements du calendrier directement dans l'action index
        $materielEvents = [];
        foreach ($materiels as $materiel) {
            $materielEvents[] = [
                'title' => $materiel->getNommat(),
                'start' => $materiel->getDateajout()->format('Y-m-d'),
            ];
        }

        // Utiliser la variable $pagination pour stocker les résultats paginés
        $pagination = $paginator->paginate(
            $materiels,
            $request->query->getInt('page', 1), // numéro de la page à afficher
            5 // nombre d'éléments par page
        );

        return $this->render('materiel/index.html.twig', [
            'materiels' => $pagination, // Passer $pagination au lieu de $paginator
            'materielEvents' => json_encode($materielEvents), // Convertir en JSON pour l'inclure dans le script Twig
        ]);
    }



    #[Route('/new/{nomParc}/{idParc}', name: 'app_materiel_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ParcRepository $parcRepository, $idParc, $nomParc, EntityManagerInterface $entityManager): Response
    {

        $parc = $parcRepository->find($idParc);

        $materiel = new Materiel();
        $materiel->setIdParc($parc);
        // Définir les valeurs automatiques
        $materiel->setNomParc($nomParc);
        $materiel->setDateAjout(new \DateTime()); // Date actuelle

        $form = $this->createForm(MaterielType::class, $materiel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $existingMateriel = $entityManager->getRepository(Materiel::class)->findOneBy(['nommat' => $materiel->getNommat()]);
            if ($existingMateriel) {
                $this->addFlash('error', 'Ce nom de materiel est déjà utilisé.');
                return $this->redirectToRoute('app_parc_index');
            }


            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($materiel);
            $entityManager->flush();

            return $this->redirectToRoute('app_parc_index');
        }

        return $this->render('materiel/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route('/{idmat}', name: 'app_materiel_show', methods: ['GET'])]
    public function show(Materiel $materiel,EntityManagerInterface $entityManager): Response
    {
        return $this->render('materiel/show.html.twig', [
            'materiel' => $materiel,
        ]);
    }

    #[Route('/{idmat}/edit', name: 'app_materiel_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Materiel $materiel, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MaterielType::class, $materiel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_materiel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('materiel/edit.html.twig', [
            'materiel' => $materiel,
            'form' => $form,
        ]);
    }

    #[Route('/{idmat}', name: 'app_materiel_delete', methods: ['POST'])]
    public function delete(Request $request, Materiel $materiel, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$materiel->getIdmat(), $request->request->get('_token'))) {
            $entityManager->remove($materiel);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_materiel_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/materiels-par-parc/{nomparc}', name: 'materiels_par_parc')]
    public function materielsParParc(string $nomparc, Request $request, MaterielRepository $materielRepository, PaginatorInterface $paginator): Response
    {
        $nommat = $request->query->get('nommat');
        $etatmat = $request->query->get('etatmat');
        $quantitemat = $request->query->get('quantitemat');
        $dateajout = $request->query->get('dateajout');

        $criteria = [
            'nommat' => $nommat,
            'etatmat' => $etatmat,
            'quantitemat' => $quantitemat,
            'dateajout' => $dateajout,
            'nomparc' => $nomparc,
        ];

        if (!empty($nommat) || !empty($etatmat) || !empty($quantitemat) || !empty($dateajout) || !empty($nomparc)) {
            // Si au moins un paramètre de recherche est fourni, utilisez la méthode searchMatByCriteria.
            $materiels = $materielRepository->searchMatByCriteria($criteria);
        } else {
            // Si aucun paramètre de recherche n'est fourni, récupérez tous les matériels.
            $materiels = $materielRepository->findBy([], ['nommat' => 'ASC']);

        }

        // Utiliser la variable $pagination pour stocker les résultats paginés
        $pagination = $paginator->paginate(
            $materiels,
            $request->query->getInt('page', 1), // numéro de la page à afficher
            5 // nombre d'éléments par page
        );

        return $this->render('parc/afficherMateriel.html.twig', [
            'materiels' => $pagination, // Passer $pagination au lieu de $paginator
        ]);
    }



}
