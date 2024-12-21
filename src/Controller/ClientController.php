<?php

namespace App\Controller;

use App\Dto\ClientFormSearch;
use App\Entity\Client;
use App\Form\ClientFormSearchType;
use App\Form\ClientType;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ClientController extends AbstractController
{
    #[Route('/boutiquier/client/liste', name: 'client.index')]
    public function index(ClientRepository $clientRepository, Request $request): Response
    {

        $clientFormSearch = new ClientFormSearch();
        $searchClientform = $this->createForm(ClientFormSearchType::class, $clientFormSearch);
        $searchClientform->handleRequest($request);

        $page = max(1, $request->query->getInt('page', 1));
        $limit = 7;
        $clients = $clientRepository->findPaginatedClients($page, $limit);
        $count = $clients->count();

        $nbrePage = ceil($count / $limit);
        $page = min($page, $nbrePage); 

        if ($searchClientform->isSubmitted() && $searchClientform->isValid()) {
            $surname = $clientFormSearch->getSurname();
            $telephone = $clientFormSearch->getTelephone();
            $statut = $clientFormSearch->getStatut();


            $clients = $clientRepository->searchClients($surname, $telephone, $statut);
        }

        return $this->render('client/index.html.twig', [
            'dataClients' => $clients,
            'searchClientform' => $searchClientform->createView(),
            'nbrePage' => $nbrePage,
            'page' => $page
        ]);
    }



    #[Route('/boutiquier/client/create', name: 'client.create')]
    public function create(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {
        $client = new Client();
        $form = $this->createForm(ClientType::class, $client);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            //Si on crée le client avec compte user
            if ($client->getUser()) {
                //Initialiser le client au compte
                $user = $client->getUser();
                $user->setClient($client);

                //Enregistrer le compte
                $entityManagerInterface->persist($user);
            }
            //S'il n'enregistre pas de compte

            $entityManagerInterface->persist($client);
            $entityManagerInterface->flush();

            return $this->redirectToRoute('client.index');
        }

        return $this->render('client/form.html.twig', [
            'formClient' => $form->createView(),
        ]);
    }



}
