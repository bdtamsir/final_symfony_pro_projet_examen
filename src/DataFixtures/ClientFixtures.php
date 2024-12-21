<?php

namespace App\DataFixtures;

use App\Entity\Articles;
use App\Entity\Client;
use App\Entity\Dette;
use App\Entity\Paiement;
use App\Entity\User;
use App\Entity\DemandeDette;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ClientFixtures extends Fixture
{

    public function __construct(private UserPasswordHasherInterface $encoder){

    }


    public function load(ObjectManager $manager): void
    {
        $articles = [];
        $clients = [];

        
        for ($i = 1; $i <= 10; $i++) {
            $article = new Articles();
            $article->setLibelle("article" . $i);
            $article->setDescription("description" . $i);
            $article->setPrix(5 + $i); 
            $article->setQteStock(100 + $i); 
            $article->setEtat("Disponible");

            $articles[$i] = $article; 
            $manager->persist($article);
        }

        
        for ($i = 1; $i <= 20; $i++) {
            $client = new Client();
            $client->setSurname("surname" . $i);
            $client->setTelephone("62359" . $i);
            $client->setAdresse("adresse" . $i);

            if ($i % 2 === 0) {
                $user = new User();
                $user->setLogin("login" . $i);
                $passwordHasher=$this->encoder->hashPassword($user,"password");
                $user->setPassword($passwordHasher);
                $user->setNom("nom" . $i);
                $user->setPrenom("prenom" . $i);
                $user->setEmail("email" . $i . "@gmail.com");
                
                $randomRole = rand(1, 3); // 1 = ROLE_ADMIN, 2 = ROLE_BOUTIQUIER, 3 = ROLE_CLIENT
                if ($randomRole === 1) {
                    $roles = ["ROLE_ADMIN"];
                } elseif ($randomRole === 2) {
                    $roles = ["ROLE_BOUTIQUIER", "ROLE_CLIENT"]; // Le boutiquier a aussi les droits du client
                } else {
                    $roles = ["ROLE_CLIENT"];
                }
                $user->setRoles($roles);
                $client->setUser($user);

                $manager->persist($user);
            }

            $clients[$i] = $client;
            $manager->persist($client);
        }

        
        foreach ($clients as $index => $client) {
            for ($j = 1; $j <= 2; $j++) { 
                $dette = new Dette();
                $dette->setDateAt(new \DateTimeImmutable());
                $dette->setMontant(1000 * $j * $index); 
                $dette->setQte(3 + $index); 
                $dette->setMontantVerser(1000 * $j * $index - 500); 
                $dette->setClient($client);

                // Ajouter des paiements
                for ($k = 1; $k <= 2; $k++) { // Chaque dette a 2 paiements
                    $paiement = new Paiement();
                    $paiement->setMontant(500 * $k);
                    $dette->addPaiement($paiement);
                    $manager->persist($paiement);
                }

                // Associer un article à la dette
                $article = $articles[array_rand($articles)];
                $dette->addArticle($article);

                $manager->persist($dette);
            }
        }

        
        foreach ($clients as $index => $client) {
            for ($j = 1; $j <= 2; $j++) { // Chaque client a 2 demandes de dette
                $demande = new DemandeDette();
                $demande->setDateAt(new \DateTimeImmutable());
                $demande->setQte(2 + $index); 
                $demande->setMontant(($articles[$j]->getPrix() * (2 + $index)));
                $demande->setClient($client);
                $demande->setArticles($articles[$j]);

                $manager->persist($demande);
            }
        }

        $manager->flush();
    }
}
