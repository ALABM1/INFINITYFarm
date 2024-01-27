<?php

namespace App\Controller;

use App\Service\PdfGenerator2;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

// Top of your controller file




use Knp\Snappy\Pdf;
use App\Service\PdfGenerator;


use Symfony\Component\Validator\Constraints\NotNull;
use Symfony\Component\Validator\Constraints\Type;



class Admin1Controller extends AbstractController
{

    #[Route('/user/change-status/{id}', name: 'change_user_status')]
    public function changeUserStatus(User $user): Response
    {
        // Inversez le statut de l'utilisateur
        $user->setStatus(!$user->getStatus());

        // Enregistrez les modifications
        $this->getDoctrine()->getManager()->flush();

        // Redirigez l'utilisateur vers la page des utilisateurs (ou toute autre page souhaitée)
        return $this->redirectToRoute('app_admin1_index');
    }


    #[Route('/admin1', name: 'app_admin1_index', methods: ['GET'])]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $nom = $request->query->get('nom');
        $prenom = $request->query->get('prenom');
        $ville = $request->query->get('ville');

        $userRepository = $entityManager->getRepository(User::class);
        $usersQuery = $userRepository->createQueryBuilder('u');

        if ($nom) {
            $usersQuery->andWhere('u.nom LIKE :nom');
            $usersQuery->setParameter('nom', '%' . $nom . '%');
        }

        if ($prenom) {
            $usersQuery->andWhere('u.prenom LIKE :prenom');
            $usersQuery->setParameter('prenom', '%' . $prenom . '%');
        }

        if ($ville) {
            $usersQuery->andWhere('u.ville LIKE :ville');
            $usersQuery->setParameter('ville', '%' . $ville . '%');
        }

        $users = $usersQuery->getQuery()->getResult();

        return $this->render('admin1/index.html.twig', [
            'users' => $users,
        ]);
    }


    public function countByVille(string $city): int
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->andWhere('u.ville = :city')
            ->setParameter('city', $city)
            ->getQuery()
            ->getSingleScalarResult();
    }
    #[Route('/chart', name: 'app_admin1_chart', methods: ['GET'])]
    public function chart(UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $cities = $this->getCitiesFromDatabase($entityManager);

        // Convert the PHP data into an array usable by Google Charts
        $chartDataArray = [['City', 'Number of Users']];
        foreach ($cities as $city) {
            $chartDataArray[] = [$city['ville'], $userRepository->countByVille($city['ville'])];
        }

        return $this->render('admin1/chart.html.twig', [
            'chartDataArray' => $chartDataArray,
        ]);
    }

    private function getCitiesFromDatabase(EntityManagerInterface $entityManager)
    {
        // Exemple de requête pour récupérer les villes
        $query = $entityManager->createQuery('
            SELECT DISTINCT u.ville
            FROM App\Entity\User u
        ');

        return $query->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }


    #[Route('/admin1/pdf', name: 'app_admin1_pdf', methods: ['GET', 'POST'])]
    public function generatePdf(Request $request, UserRepository $userRepository, PdfGenerator $pdfGenerator): Response
    {
        $signaturePath = null;

        if ($request->isMethod('POST')) {
            // Spécifiez le chemin de l'image (par exemple, dans le répertoire "public/img")
            $signaturePath = $this->getParameter('kernel.project_dir') . '/public/img/signature.png';
        }

        $users = $userRepository->findAll();
        $pdfContent = $pdfGenerator->generatePdf($users, $signaturePath);

        $filename = 'user_list.pdf';

        return new Response(
            $pdfContent,
            200,
            [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="' . $filename . '"',
            ]
        );
    }

    #[Route('/admin1/new', name: 'app_admin1_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin1_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin1/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('admin1/{id}', name: 'app_admin1_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin1/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('admin1/{id}/edit', name: 'app_admin1_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($id);

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur non trouvé');
        }

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('admin1/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('admin1/{id}', name: 'app_admin1_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_admin1_index', [], Response::HTTP_SEE_OTHER);
    }
    #[Route('/chart2', name: 'app_admin2_chart', methods: ['GET'])]
    public function genderChart(UserRepository $userRepository): Response
    {
        // Get the count of men
        $numberOfMen = $userRepository->countBySexe('Homme');

        // Get the count of women
        $numberOfWomen = $userRepository->countBySexe('Femme');

        // Get the count of workers
        $numberOfWorkers = $userRepository->countByRole('OUVRIER');

        // Get the count of farmers
        $numberOfFarmers = $userRepository->countByRole('AGRICULTEUR');

        // Render the chart view with the counts
        return $this->render('admin1/chart2.html.twig', [
            'numberOfMen' => $numberOfMen,
            'numberOfWomen' => $numberOfWomen,

            'numberOfWorkers' => $numberOfWorkers,
            'numberOfFarmers' => $numberOfFarmers,
        ]);
    }
    // User Repository
    public function countBySexe(string $gender): int
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->andWhere('u.sexe = :gender') // Adjust 'gender' to match your actual field name
            ->setParameter('gender', $gender)
            ->getQuery()
            ->getSingleScalarResult();
    }
    public function countByRole(string $role): int
    {
        return $this->createQueryBuilder('u')
            ->select('COUNT(u.id)')
            ->andWhere('u.roles LIKE :role')
            ->setParameter('role', '%"'.$role.'"%') // Assuming roles are stored as an array
            ->getQuery()
            ->getSingleScalarResult();
    }

}
