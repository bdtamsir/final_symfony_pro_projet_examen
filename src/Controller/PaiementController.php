<?php

namespace App\Controller;

use App\Repository\DetteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\PaiementRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Paiement;
use App\Form\PaiementType;
use Doctrine\ORM\EntityManagerInterface;
use App\Dto\PaiementFormSearch;
use App\Form\PaiementFormSearchType;

class PaiementController extends AbstractController
{
    #[Route('/client/paiements/dette/{idDette}', name: 'paiement.index')]
    public function index($idDette, PaiementRepository $paiementRepository, Request $request, DetteRepository $detteRepository, EntityManagerInterface $entityManagerInterface): Response
    {

        $dette = $detteRepository->find($idDette);
        $page = max(1,$request->query->getInt('page', 1));
        $limit = 5;
        $paiements = $paiementRepository->findByDette($idDette, $page, $limit);
        $count = $paiements->count();

        $nbrePage = ceil($count / $limit);
        $page = min($page, $nbrePage); 

        //Enregistrement d'un paiement sur la meme page

        $paiement = new Paiement();
        $form = $this->createForm(PaiementType::class, $paiement);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $montantRestant = $dette->getMontant() - $dette->getMontantVerser();
            if ($paiement->getMontant() > $montantRestant) {
                $this->addFlash('error','Attention ⚠️ ! Le montant du paiement est supérieur à celui restant');
            } else {
                $dette->setMontantVerser($dette->getMontantVerser() + $paiement->getMontant());
                $paiement->setDette($dette);
                $entityManagerInterface->persist($paiement);
                $entityManagerInterface->flush();
            }

            return $this->redirectToRoute('paiement.index', [
                'idDette' => $idDette
            ]);
        }

        return $this->render('paiement/index.html.twig', [
            'paiements' => $paiements,
            'nbrePage' => $nbrePage,
            'page' => $page,
            'dette' => $dette,
            'form' => $form,
            'disabled' => $dette->getMontant() == $dette->getMontantVerser()
        ]);
    }


    #[Route('/boutiquier/paiement/historique', name: 'paiement.historique')]
    public function paiementHistorique(PaiementRepository $paiementRepository, Request $request): Response
    {

        $paiementFormSearch = new PaiementFormSearch();
        $searchPaiementForm = $this->createForm(PaiementFormSearchType::class, $paiementFormSearch);
        $searchPaiementForm->handleRequest($request);

        $page=max(1,$request->query->getInt('page',1));
        $limit=7;
        $paiements = $paiementRepository->findPaginatedPaiements($page,$limit);
        $count=$paiements->count();

        $nbrePage=ceil($count/$limit);
        $page = min($page, $nbrePage);

        if ($searchPaiementForm->isSubmitted() && $searchPaiementForm->isValid()) {
            $surname = $paiementFormSearch->getSurname();

          
            $paiements = $paiementRepository->searchPaiements($surname);
        }

        return $this->render('paiement/historique.html.twig', [
            'paiements' => $paiements,
            'nbrePage' => $nbrePage,
            'page' => $page,
            'searchPaiementForm' => $searchPaiementForm->createView(),
        ]);
    }
}
