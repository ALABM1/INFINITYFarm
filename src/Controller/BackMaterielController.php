<?php

namespace App\Controller;

use App\Entity\Materiel;
use App\Form\MaterielType;
use App\Repository\MaterielRepository;
use App\Repository\ParcRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/back/materiel')]
class BackMaterielController extends AbstractController
{
    private ParcRepository $parcRepository;

    #[Route('/', name: 'app_back_materiel_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $materiels = $entityManager
            ->getRepository(Materiel::class)
            ->findAll();

        return $this->render('back_materiel/index.html.twig', [
            'materiels' => $materiels,
        ]);
    }

    #[Route('/new/{nomParc}/{idParc}', name: 'app_back_materiel_new', methods: ['GET', 'POST'])]
    public function new(Request $request, ParcRepository $parcRepository, $idParc, $nomParc): Response
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

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($materiel);
            $entityManager->flush();

            return $this->redirectToRoute('app_back_parc_index');
        }

        return $this->render('back_materiel/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }



    #[Route('/{idmat}', name: 'app_back_materiel_show', methods: ['GET'])]
    public function show(Materiel $materiel,EntityManagerInterface $entityManager): Response
    {
        return $this->render('back_materiel/show.html.twig', [
            'materiel' => $materiel,
        ]);
    }

    #[Route('/{idmat}/edit', name: 'app_back_materiel_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Materiel $materiel, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(MaterielType::class, $materiel);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_back_materiel_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('back_materiel/edit.html.twig', [
            'materiel' => $materiel,
            'form' => $form,
        ]);
    }

    #[Route('/{idmat}', name: 'app_back_materiel_delete', methods: ['POST'])]
    public function delete(Request $request, Materiel $materiel, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$materiel->getIdmat(), $request->request->get('_token'))) {
            $entityManager->remove($materiel);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_back_materiel_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/materiels-par-parc/{nomparc}', name: 'materiels_par_parc_back')]
    public function materielsParParc(string $nomparc, MaterielRepository $materielRepository): Response
    {
        // Utilisez la méthode du repository pour récupérer les matériels par nom de parc
        $materiels = $materielRepository->findMaterielsByNomParc($nomparc);

        // Envoyez les résultats à la vue (template)
        return $this->render('parc/afficherMateriel.html.twig', [
            'materiels' => $materiels,
        ]);
    }

}
