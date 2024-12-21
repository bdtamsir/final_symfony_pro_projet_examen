<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\DetteRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Dto\DetteFormSearch;
use App\Form\DetteFormSearchType;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\DetteType;
use App\Entity\Dette;
use Doctrine\ORM\Tools\Pagination\Paginator;

class DetteController extends AbstractController
{
    #[Route('/client/dette/{idClient}/client', name: 'dette.index')]
    public function index($idClient, DetteRepository $detteRepository, Request $request): Response
    {

        $detteFormSearch = new DetteFormSearch();
        $searchDetteForm = $this->createForm(DetteFormSearchType::class, $detteFormSearch);
        $searchDetteForm->handleRequest($request);


        $page = max(1, $request->query->getInt('page', 1));
        $limit = 3;


        if ($searchDetteForm->isSubmitted() && $searchDetteForm->isValid()) {
            $surname = $detteFormSearch->getSurname();
            $telephone = $detteFormSearch->getTelephone();
            $statut = $detteFormSearch->getStatut();

            $dettes = $detteRepository->searchDettes($surname, $telephone, $statut, $page, $limit);
        } else {
            $dettes = $detteRepository->findByClient($idClient, $page, $limit);
        }


        $count = $dettes->count();
        $nbrePage = ceil($count / $limit);

        return $this->render('dette/index.html.twig', [
            'dettes' => $dettes,
            'searchDetteForm' => $searchDetteForm->createView(),
            'nbrePage' => $nbrePage,
            'page' => $page
        ]);
    }








    #[Route('/boutiquier/dettes/liste', name: 'dette.allDettes')]
    public function allDettes(DetteRepository $detteRepository, Request $request): Response
    {
        $detteFormSearch = new DetteFormSearch();
        $searchDetteForm = $this->createForm(DetteFormSearchType::class, $detteFormSearch);
        $searchDetteForm->handleRequest($request);

        $page = max(1, $request->query->getInt('page', 1));
        $limit = 9;

        if ($searchDetteForm->isSubmitted() && $searchDetteForm->isValid()) {
            $surname = $detteFormSearch->getSurname();
            $telephone = $detteFormSearch->getTelephone();
            $statut = $detteFormSearch->getStatut();

            $dettes = $detteRepository->searchDettes($surname, $telephone, $statut, $page, $limit);
        } else {
            $dettes = $detteRepository->findAllDettes($page, $limit);
        }

        // Récupérer le total des résultats
        $totalResults = $dettes->count();

        // Calculer le nombre de pages
        $nbrePage = ceil($totalResults / $limit);

        // Assurer que la page demandée est valide
        $page = min($page, $nbrePage);

        return $this->render('dette/index.html.twig', [
            'dettes' => $dettes,
            'searchDetteForm' => $searchDetteForm->createView(),
            'nbrePage' => $nbrePage,
            'page' => $page
        ]);
    }










    #[Route('/boutiquier/dettes/create', name: 'dette.create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $dette = new Dette();
        $form = $this->createForm(DetteType::class, $dette);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $totalMontant = 0;
            $articles = $dette->getArticles();
            $quantite = $dette->getQte();

            // Vérification et calcul du montant total
            foreach ($articles as $article) {
                if ($quantite > $article->getQteStock()) {
                    $this->addFlash('danger', 'La quantité demandée dépasse le stock disponible pour l\'article ' . $article->getLibelle());
                    return $this->redirectToRoute('dette.create'); 
                }

                // Calcul du montant total
                $totalMontant += $article->getPrix() * $quantite;

                // Mise à jour de la quantité en stock
                $article->setQteStock($article->getQteStock() - $quantite);
            }

            $dette->setMontant($totalMontant);

            // Validation du montant versé
            if ($dette->getMontantVerser() > $totalMontant) {
                $this->addFlash('danger', 'Le montant versé ne peut pas être supérieur au montant total.');
                return $this->redirectToRoute('dette.create');
            }

            $entityManager->persist($dette);
            $entityManager->flush();

            return $this->redirectToRoute('dette.allDettes');
        }

        return $this->render('dette/form.html.twig', [
            'formDette' => $form->createView(),
        ]);
    }






    /////////-------------------------ARCHIVER DETTE SOLDEE-------------------/////////////

    #[Route('/admin/dettes/solded', name: 'dette.solded')]
    public function listSoldedDebts(DetteRepository $detteRepository, Request $request): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 10;
        $dettes = $detteRepository->findSoldedDebts($page, $limit);

        return $this->render('dette/solded.html.twig', [
            'dettes' => $dettes,
            'page' => $page,
            'nbrePage' => ceil($dettes->count() / $limit),
        ]);
    }

    #[Route('/admin/dettes/{id}/archive', name: 'dette.archive')]
    public function archiveDebt(Dette $dette, EntityManagerInterface $entityManager): Response
    {
        $dette->setArchived(true);
        $entityManager->flush();

        return $this->redirectToRoute('dette.solded');
    }

    #[Route('/admin/dettes/archived', name: 'dette.archived')]
    public function listArchivedDebts(DetteRepository $detteRepository, Request $request): Response
    {
        $page = max(1, $request->query->getInt('page', 1));
        $limit = 10;
        $query = $detteRepository->createQueryBuilder('d')
            ->where('d.archived = true') 
            ->orderBy('d.id', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery();

        $dettes = new Paginator($query);

        return $this->render('dette/archived.html.twig', [
            'dettes' => $dettes,
            'page' => $page,
            'nbrePage' => ceil($dettes->count() / $limit),
        ]);
    }
}
