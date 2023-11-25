<?php

namespace App\Controller;

use App\Entity\Users;
use App\Form\Users1Type;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $searchTerm = $request->query->get('search');

        if ($searchTerm) {
            $users = $entityManager->getRepository(Users::class)->findByNom($searchTerm);
        } else {
            $users = $entityManager->getRepository(Users::class)->findAll();
        }

        return $this->render('admin/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/admin/new', name: 'app_admin_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $user = new Users();
        $form = $this->createForm(Users1Type::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si l'email existe déjà dans la base de données
            $existingUser = $entityManager->getRepository(Users::class)->findOneBy(['mail' => $user->getMail()]);

            if ($existingUser) {
                // Gérer le cas où l'email existe déjà
                $this->addFlash('error', 'Cet email est déjà utilisé.');

                return $this->redirectToRoute('app_admin_new');
            }

            // Si l'email n'existe pas, procéder à l'ajout
            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash('success', 'Utilisateur ajouté avec succès.');

            return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        // Afficher les erreurs de validation dans le template
        $errors = $validator->validate($user);

        return $this->render('admin/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'errors' => $errors,
        ]);
    }


    #[Route('/admin/{id}', name: 'app_admin_show', methods: ['GET'])]
    public function show(Users $user): Response
    {
        return $this->render('admin/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/admin/{id}/edit', name: 'app_admin_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Users $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Users1Type::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/admin/{id}', name: 'app_admin_delete', methods: ['POST'])]
    public function delete(Request $request, Users $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
    }
}
