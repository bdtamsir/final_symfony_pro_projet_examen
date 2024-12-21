<?php

namespace App\Controller;

use App\Dto\NoAccountClientSearch;
use App\Entity\User;
use App\Entity\Client;
use App\Form\NoAccountClientSearchType;
use App\Repository\UserRepository;
use App\Repository\ClientRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use App\Form\UserType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserController extends AbstractController
{
    #[Route('/admin/users/liste', name: 'user.index')]
    public function index(UserRepository $userRepository, Request $request): Response
    {

        $page = max(1, $request->query->getInt('page', 1));
        $limit = 7;
        $users = $userRepository->findPaginatedUsers($page, $limit);
        $count = $users->count();

        $nbrePage = ceil($count / $limit);
        $page = min($page, $nbrePage); 

        $roleFilter = $request->query->get('role', null); 

        
        if ($roleFilter) {
            $users = $userRepository->findByRole($roleFilter);
        } else {
            $users = $userRepository->findPaginatedUsers($page, $limit);
        }

        return $this->render('user/index.html.twig', [
            'users' => $users,
            'nbrePage' => $nbrePage,
            'page' => $page,
            'currentRole' => $roleFilter, 
        ]);
    }





    #[Route('/admin/users/create', name: 'user.create')]
    public function create(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Hachage du mot de passe
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);
            
            $surname = $form->get('surname')->getData();
            $telephone = $form->get('telephone')->getData();
            $adresse = $form->get('adresse')->getData();

            
            $client = new Client();
            $client->setSurname($surname);
            $client->setTelephone($telephone);
            $client->setAdresse($adresse);
            $client->setUser($user);
            $user->setClient($client);

            $entityManager->persist($client);
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user.index');
        }

        return $this->render('user/form.html.twig', [
            'formUser' => $form->createView(),
        ]);
    }




    #[Route('/admin/admin/clients-without-user', name: 'admin.clients_without_user')]
    public function clientsWithoutUser(ClientRepository $clientRepository, Request $request): Response
    {

        $noAccountClientSearch = new NoAccountClientSearch();
        $searchNoAccountClientform = $this->createForm(NoAccountClientSearchType::class, $noAccountClientSearch);
        $searchNoAccountClientform->handleRequest($request);

        $page = max(1, $request->query->getInt('page', 1));
        $limit = 10;

        $clients = $clientRepository->findClientsWithoutUser($page, $limit);
        $count = $clients->count();

        $nbrePage = ceil($count / $limit);
        $page = min($page, $nbrePage);

        if ($searchNoAccountClientform->isSubmitted() && $searchNoAccountClientform->isValid()) {
            $surname = $noAccountClientSearch->getSurname();
            $telephone = $noAccountClientSearch->getTelephone();


            $clients = $clientRepository->searchClientNoAccount($surname, $telephone);
        }

        return $this->render('user/clients_without_user.html.twig', [
            'clients' => $clients,
            'searchNoAccountClientform' => $searchNoAccountClientform->createView(),
            'nbrePage' => $nbrePage,
            'page' => $page,
        ]);
    }



    #[Route('/admin/admin/user/create/{id}', name: 'user.create_for_client')]
    public function createForClient(Client $client, Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        // Vérification si le client a déjà un compte utilisateur
        if ($client->getUser()) {
            return $this->redirectToRoute('user.clients_without_user');
        }

        // Initialisation de l'utilisateur
        $user = new User();
        $user->setClient($client); 

        // Création du formulaire
        $form = $this->createForm(UserType::class, $user, [
            'exclude_fields' => ['surname', 'telephone', 'adresse'], // Exclure les champs du client
        ]);
        $form->handleRequest($request);

        // Traitement du formulaire
        if ($form->isSubmitted() && $form->isValid()) {
            $hashedPassword = $passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('admin.clients_without_user');
        }

        // Affichage du formulaire
        return $this->render('user/form.html.twig', [
            'formUser' => $form->createView(),
            'client' => $client, 
        ]);
    }
}
