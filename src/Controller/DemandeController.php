<?php

namespace App\Controller;

use App\Dto\DemandeFormSearch;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\DemandeDetteRepository;
use App\Entity\DemandeDette;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Dette;
use App\Entity\Articles;
use App\Form\DemandeFormSearchType;
use Symfony\Component\HttpFoundation\Request;
use App\Form\DemandeDetteType;

class DemandeController extends AbstractController
{
    #[Route('/boutiquier/demandes', name: 'demande.liste')]
    public function listeDemandes(DemandeDetteRepository $demandeDetteRepository, Request $request): Response
    {
        $demandeFormSearch = new DemandeFormSearch();
        $searchDemandeForm = $this->createForm(DemandeFormSearchType::class, $demandeFormSearch);
        $searchDemandeForm->handleRequest($request);

        $page=max(1,$request->query->getInt('page',1));
        $limit=7;
        $demandes = $demandeDetteRepository->findPaginatedDemandeDette($page,$limit);
        $count=$demandes->count();

        $nbrePage=ceil($count/$limit);
        $page = min($page, $nbrePage); 

        if ($searchDemandeForm->isSubmitted() && $searchDemandeForm->isValid()) {
            $surname = $demandeFormSearch->getSurname();
            $demandes = $demandeDetteRepository->searchDemande($surname);
        }


        return $this->render('demande/index.html.twig', [
            'demandes' => $demandes,
            'nbrePage'=>$nbrePage,
            'page'=>$page,
            'searchDemandeForm' => $searchDemandeForm->createView()
        ]);
    }




    #[Route('/client/demandes/{id}', name: 'demande.details')]
    public function detailsDemande(DemandeDette $demande): Response
    {
        return $this->render('demande/details.html.twig', [
            'demande' => $demande,
        ]);
    }






    #[Route('/boutiquier/demandes/{id}/accepter', name: 'demande.accepter')]
    public function accepterDemande(DemandeDette $demande, EntityManagerInterface $entityManager): Response
    {
        $article = $demande->getArticles(); 
        $qteDemandee = $demande->getQte();
    
        // Validation : vérifier que le stock est suffisant
        if ($qteDemandee > $article->getQteStock()) {
            $this->addFlash('danger', 'La quantité demandée dépasse le stock disponible pour l\'article ' . $article->getLibelle());
            return $this->redirectToRoute('demande.details', ['id' => $demande->getId()]);
        }
    
        // Mise à jour du stock
        $article->setQteStock($article->getQteStock() - $qteDemandee);
    
        // Création de la dette
        $dette = new Dette();
        $dette->setClient($demande->getClient());
        $dette->setQte($qteDemandee);
        $dette->setMontant($demande->getMontant());
        $dette->setMontantVerser(0);
        $dette->setDateAt(new \DateTimeImmutable());
    
        // Ajouter l'article à la dette
        $dette->addArticle($article);
    
        // Persist la dette et supprimer la demande
        $entityManager->persist($dette);
        $entityManager->remove($demande);
        $entityManager->flush();
    
        return $this->redirectToRoute('demande.liste');
    }
    
    



    #[Route('/boutiquier/demandes/{id}/refuser', name: 'demande.refuser')]
    public function refuserDemande(DemandeDette $demande, EntityManagerInterface $entityManager): Response
    {
        // Supprimer la demande de la base de données
        $entityManager->remove($demande);
        $entityManager->flush();

    
        // Rediriger vers la liste des demandes
        return $this->redirectToRoute('demande.liste');
    }




    /////////////LISTER POUR CLIENT////////////////////

    #[Route('/client/demandes/{idClient}/client', name: 'demande.index')]
    public function index($idClient, DemandeDetteRepository $demandeDetteRepository, Request $request): Response
    {
        $demandeFormSearch = new DemandeFormSearch();
        $searchDemandeForm = $this->createForm(DemandeFormSearchType::class, $demandeFormSearch);
        $searchDemandeForm->handleRequest($request);

        $page=max(1,$request->query->getInt('page',1));
        $limit=7;
        $demandes = $demandeDetteRepository->findByClientDemandes($idClient,$page,$limit);
        $count=$demandes->count();

        $nbrePage=ceil($count/$limit);
        $page = min($page, $nbrePage); // Pas plus que le nombre total de pages

        if ($searchDemandeForm->isSubmitted() && $searchDemandeForm->isValid()) {
            $surname = $demandeFormSearch->getSurname();
            $demandes = $demandeDetteRepository->searchDemande($surname);
        }


        return $this->render('demande/index.html.twig', [
            'demandes' => $demandes,
            'nbrePage'=>$nbrePage,
            'page'=>$page,
            'searchDemandeForm' => $searchDemandeForm->createView()
        ]);
    }




    ////////CREER DETTE////////////////////

    #[Route('/demandes/create', name: 'demande.create')]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $demande = new DemandeDette();
        $user = $this->getUser();
    
        // Si l'utilisateur est un client, associer automatiquement le client
        if (in_array('ROLE_CLIENT', $user->getRoles())) {
            $client = $user->getClient();
            $demande->setClient($client);
        }
    
        $form = $this->createForm(DemandeDetteType::class, $demande);
        $form->handleRequest($request);
    
        if ($form->isSubmitted() && $form->isValid()) {
            $demande->setDateAt(new \DateTimeImmutable());
    
            // Calculer le montant
            $article = $demande->getArticles();
            $montant = $article->getPrix() * $demande->getQte();
            $demande->setMontant($montant);
    
            $entityManager->persist($demande);
            $entityManager->flush();
    
            // Redirection conditionnelle
            if (in_array('ROLE_BOUTIQUIER', $user->getRoles())) {
                return $this->redirectToRoute('demande.liste');
            } elseif (in_array('ROLE_CLIENT', $user->getRoles())) {
                $client = $user->getClient();
                if ($client) {
                    return $this->redirectToRoute('demande.index', ['idClient' => $client->getId()]);
                }
            }
    
            return $this->redirectToRoute('app_home');
        }
    
        return $this->render('demande/form.html.twig', [
            'formDemande' => $form->createView(),
        ]);
    }
    

}
