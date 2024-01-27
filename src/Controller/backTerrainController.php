<?php

namespace App\Controller;

use App\Entity\Terrain;
use App\Entity\Ressource;
use App\Form\TerrainType;
use App\Form\RessourceType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/backterrain')]
class backTerrainController extends AbstractController
{
    #[Route('/', name: 'app_backterrain_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $terrains = $entityManager
            ->getRepository(Terrain::class)
            ->findAll();

        return $this->render('backterrain/index.html.twig', [
            'terrains' => $terrains,
        ]);
    }

    #[Route('/new', name: 'app_backterrain_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $terrain = new Terrain();
        $form = $this->createForm(TerrainType::class, $terrain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($terrain);
            $entityManager->flush();

            return $this->redirectToRoute('app_backterrain_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backterrain/new.html.twig', [
            'terrain' => $terrain,
            'form' => $form,
        ]);
    }

    #[Route('/{idterrain}', name: 'app_backterrain_show', methods: ['GET'])]
    public function show(Terrain $terrain): Response
    {
        $ressources = $terrain->getRessources(); // Fetch resources associated with the terrain

        return $this->render('backterrain/show.html.twig', [
            'terrain' => $terrain,
            'ressources' => $ressources, // Pass the resources to the template
        ]);
    }

    #[Route('/{idterrain}/edit', name: 'app_backterrain_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Terrain $terrain, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TerrainType::class, $terrain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_backterrain_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('backterrain/edit.html.twig', [
            'terrain' => $terrain,
            'form' => $form,
        ]);
    }

    #[Route('/{idterrain}', name: 'app_backterrain_delete', methods: ['POST'])]
    public function delete(Request $request, Terrain $terrain, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $terrain->getIdterrain(), $request->request->get('_token'))) {
            $entityManager->remove($terrain);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_backterrain_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{idterrain}/edit-ressource/{idres}', name: 'app_backterrain_edit_ressource', methods: ['GET', 'POST'])]
    public function editRessource(Request $request, Terrain $terrain, Ressource $ressource, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RessourceType::class, $ressource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_backterrain_show', ['idterrain' => $terrain->getIdterrain()]);
        }

        return $this->renderForm('backressource/edit.html.twig', [
            'ressource' => $ressource,
            'form' => $form,
        ]);
    }

    #[Route('/{idterrain}/delete-ressource/{idres}', name: 'app_backterrain_delete_ressource', methods: ['GET'])]
    public function deleteRessource(Terrain $terrain, Ressource $ressource, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($ressource);
        $entityManager->flush();

        return $this->redirectToRoute('app_backterrain_show', ['idterrain' => $terrain->getIdterrain()]);
    }

    #[Route('/{idterrain}/new-ressource', name: 'app_backterrain_new_ressource', methods: ['GET', 'POST'])]
    public function newRessource(Request $request, Terrain $terrain, EntityManagerInterface $entityManager): Response
    {
        $ressource = new Ressource();
        $ressource->setIdterrain($terrain);

        $form = $this->createForm(RessourceType::class, $ressource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ressource);
            $entityManager->flush();

            return $this->redirectToRoute('app_backterrain_show', ['idterrain' => $terrain->getIdterrain()]);
        }

        return $this->renderForm('backressource/new.html.twig', [
            'ressource' => $ressource,
            'form' => $form,
        ]);
    }

}
