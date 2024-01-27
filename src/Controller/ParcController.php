<?php

namespace App\Controller;

use App\Entity\Parc;
use App\Form\ParcType;
use App\Form\SearchParcType;
use App\Repository\MaterielRepository;
use App\Repository\ParcRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use TCPDF;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\User;



#[Route('/parc')]
class ParcController extends AbstractController
{

    #[Route('/', name: 'app_parc_index', methods: ['GET'])]
    public function index(Request $request,EntityManagerInterface $entityManager, ParcRepository $parcRepository ,PaginatorInterface $paginator): Response
    {

        // Accédez à l'utilisateur connecté
        // Accédez à l'utilisateur connecté
        $user = $this->getUser();

        // Vérifiez si l'utilisateur est connecté
        if ($user instanceof UserInterface) {
            // Accédez aux propriétés de l'utilisateur connecté
            $userId = $user->getId();

            // Récupérez les parcs appartenant à l'utilisateur connecté
            $parcs = $parcRepository->findBy(['user' => $userId]);
        } else {
            // Si l'utilisateur n'est pas connecté, redirigez-le vers la page de connexion
            return $this->redirectToRoute('app_login');
        }

        ///////////////////////////////////////////////////////////
        $nomparc = $request->query->get('nomparc');
        $adresseparc = $request->query->get('adresseparc');
        $superficieparc = $request->query->get('superficieparc');
        $sortBy = $request->query->get('sortBy');
        $sortBySuperficie = $request->query->get('sortBySuperficie'); // Nouveau paramètre



        $criteria = [
            'nomparc' => $nomparc,
            'adresseparc' => $adresseparc,
            'superficieparc' => $superficieparc,

        ];

        if (!empty($nomparc) || !empty($adresseparc) || !empty($superficieparc)) {
            // If at least one search parameter is provided, use the searchByCriteria method.
            $parcs = $parcRepository->searchByCriteria($criteria);
        } else {
            // If no search parameter is provided, retrieve all parcs.
            if ($sortBy === 'asc') {
                $parcs = $parcRepository->findAllSortedByNameAsc();
            } elseif ($sortBySuperficie === 'desc') {
                $parcs = $parcRepository->findAllSortedBySuperficieDesc();
            } else {
                $parcs = $parcRepository->findAll();
            }
        }

        $pagination = $paginator->paginate(
            $parcs,
            $request->query->getInt('page', 1), // numéro de la page à afficher
            5 // nombre d'éléments par page
        );

        return $this->render('parc/index.html.twig', [
            'parcs' => $pagination,
        ]);
    }



    #[Route('/new', name: 'app_parc_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $parc = new Parc();
        $form = $this->createForm(ParcType::class, $parc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Convertir la superficie du parc en hectares
            $superficieEnHectares = $parc->getSuperficieParc() / 10000;
            $parc->setSuperficieParc($superficieEnHectares);

            // Le nom du parc est unique, persistez et flush
            $entityManager->persist($parc);
            $entityManager->flush();

            $this->addFlash('done', 'Parc ajouté avec succès!');

            return $this->redirectToRoute('app_parc_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('parc/new.html.twig', [
            'parc' => $parc,
            'form' => $form,
        ]);
    }
    #[Route('/{idparc}', name: 'app_parc_show', methods: ['GET'])]
    public function show(Parc $parc): Response
    {
        return $this->render('parc/show.html.twig', [
            'parc' => $parc,
        ]);
    }

    #[Route('/{idparc}/edit', name: 'app_parc_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Parc $parc, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ParcType::class, $parc);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $superficieEnHectares = $parc->getSuperficieParc() / 10000;
            $parc->setSuperficieParc($superficieEnHectares);
            $entityManager->flush();

            return $this->redirectToRoute('app_parc_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('parc/edit.html.twig', [
            'parc' => $parc,
            'form' => $form,
        ]);
    }

    #[Route('/{idparc}', name: 'app_parc_delete', methods: ['POST'])]
    public function delete(Request $request, Parc $parc, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$parc->getIdparc(), $request->request->get('_token'))) {
            $entityManager->remove($parc);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_parc_index', [], Response::HTTP_SEE_OTHER);
    }
    private function getParcNames(EntityManagerInterface $entityManager): array
    {
        $parcs = $entityManager
            ->getRepository(Parc::class)
            ->findAll();

        $parcNames = [];

        foreach ($parcs as $parc) {
            $parcNames[] = $parc->getNomParc();
        }

        return $parcNames;
    }


    #[Route('/findbyname', name: 'find_name_parc', methods: ['GET'])]
    public function findByName(Request $request, EntityManagerInterface $entityManager): Response
    {
        $nom = $request->get('nom');

        $qb = $entityManager->createQueryBuilder();
        $qb->select('p')
            ->from('App\Entity\Parc', 'p')
            ->where('p.nomparc = :nom')
            ->setParameter('nom', $nom);

        $parc = $qb->getQuery()->getOneOrNullResult();

        if ($parc) {
            return $this->render('parc/index.html.twig', [
                'parc' => $parc,
            ]);
        } else {
            return $this->render('parc/index.html.twig', [
                'parcs' => [],
                'nom' => $nom,
                'error' => 'No park found with the name: ' . $nom,
            ]);
        }
    }


}
