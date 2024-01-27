<?php

namespace App\Controller;

use App\Entity\Ressource;
use App\Entity\Terrain;
use App\Form\RessourceType;
use App\Form\TerrainType;
use App\Repository\TerrainRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/terrain')]
class TerrainController extends AbstractController
{
    #[Route('/', name: 'app_terrain_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager, TerrainRepository $terrainRepository): Response
    {
        $nomterrain = $request->query->get('nomterrain');
        $localisation = $request->query->get('localisation');
        $superficie = $request->query->get('superficie');

        $criteria = [
            'nomTerrain' => $nomterrain,
            'localisation' => $localisation,
            'superficie' => $superficie,
        ];
        $terrains = $terrainRepository->searchByCriteria($criteria);


        // Ajoutez la logique de tri
        $sortField = $request->query->get('sort', 'nomterrain');
        $sortOrder = $request->query->get('order', 'asc');

        // Validez les paramètres de tri pour éviter les injections SQL
        $allowedFields = ['nomterrain', 'localisation', 'superficie'];
        if (!in_array($sortField, $allowedFields) || !in_array(strtolower($sortOrder), ['asc', 'desc'])) {
            throw new \InvalidArgumentException('Invalid sorting parameters.');
        }

        if (!empty($nomterrain) || !empty($localisation) || !empty($superficie)) {
            // Si au moins un paramètre de recherche est fourni, utilisez la méthode searchByCriteria.
            $terrains = $terrainRepository->searchByCriteria($criteria, $sortField, $sortOrder);
        } else {
            // Si aucun paramètre de recherche n'est fourni, récupérez tous les terrains avec tri.
            $terrains = $terrainRepository->findBy([], [$sortField => $sortOrder]);
        }

        return $this->render('terrain/index.html.twig', [
            'terrains' => $terrains,
            'sortField' => $sortField,
            'sortOrder' => $sortOrder,
        ]);
    }



    #[Route('/new', name: 'app_terrain_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $terrain = new Terrain();
        $form = $this->createForm(TerrainType::class, $terrain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($terrain);
            $entityManager->flush();

            // Ajoutez le message flash ici
            $this->addFlash('success', 'Le nouveau terrain a été ajouté avec succès.');

            return $this->redirectToRoute('app_terrain_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('terrain/new.html.twig', [
            'terrain' => $terrain,
            'form' => $form,
        ]);
    }


    #[Route('/{idterrain}', name: 'app_terrain_show', methods: ['GET'])]
    public function show(Terrain $terrain): Response
    {
        $ressources = $terrain->getRessources();

        return $this->render('terrain/show.html.twig', [
            'terrain' => $terrain,
            'ressources' => $ressources,
        ]);
    }

    #[Route('/{idterrain}/edit', name: 'app_terrain_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Terrain $terrain, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TerrainType::class, $terrain);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_terrain_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('terrain/edit.html.twig', [
            'terrain' => $terrain,
            'form' => $form,
        ]);
    }

    #[Route('/{idterrain}', name: 'app_terrain_delete', methods: ['POST'])]
    public function delete(Request $request, Terrain $terrain, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $terrain->getIdterrain(), $request->request->get('_token'))) {
            $entityManager->remove($terrain);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_terrain_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/terrain/{idterrain}/edit-ressource/{idres}', name: 'app_terrain_edit_ressource', methods: ['GET', 'POST'])]
    public function editRessource(Request $request, Terrain $terrain, Ressource $ressource, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RessourceType::class, $ressource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            // Redirect back to the terrain show page after editing the resource
            return $this->redirectToRoute('app_terrain_show', ['idterrain' => $terrain->getIdterrain()]);
        }

        return $this->renderForm('ressource/edit.html.twig', [
            'ressource' => $ressource,
            'form' => $form,
        ]);
    }

    #[Route('/terrain/{idterrain}/delete-ressource/{idres}', name: 'app_terrain_delete_ressource', methods: ['GET'])]
    public function deleteRessource(Terrain $terrain, Ressource $ressource, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($ressource);
        $entityManager->flush();

        // Redirect back to the terrain show page after deleting the resource
        return $this->redirectToRoute('app_terrain_show', ['idterrain' => $terrain->getIdterrain()]);
    }

    #[Route('/terrain/{idterrain}/new-ressource', name: 'app_terrain_new_ressource', methods: ['GET', 'POST'])]
    public function newRessource(Request $request, Terrain $terrain, EntityManagerInterface $entityManager): Response
    {
        $ressource = new Ressource();
        $ressource->setIdterrain($terrain); // Set the associated terrain

        $form = $this->createForm(RessourceType::class, $ressource);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($ressource);
            $entityManager->flush();

            // Redirect back to the terrain show page after adding a new resource
            return $this->redirectToRoute('app_terrain_show', ['idterrain' => $terrain->getIdterrain()]);
        }

        return $this->renderForm('ressource/new.html.twig', [
            'ressource' => $ressource,
            'form' => $form,
        ]);
    }


}
