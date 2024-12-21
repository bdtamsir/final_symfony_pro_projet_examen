<?php

namespace App\Repository;

use App\Entity\Client;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\DBAL\Query\Limit;

/**
 * @extends ServiceEntityRepository<Client>
 */
class ClientRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Client::class);
    }

    //    /**
    //     * @return Client[] Returns an array of Client objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('c.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }
    public function searchClients(?string $surname, ?string $telephone, ?string $statut): array
    {
        $qb = $this->createQueryBuilder('c');

        if (!empty($surname)) {
            $qb->andWhere('c.surname = :surname')
                ->setParameter('surname', $surname);
        }

        if (!empty($telephone)) {
            $qb->andWhere('c.telephone = :telephone')
                ->setParameter('telephone', $telephone);
        }

        if ($statut === 'Oui') {
            // avec un compte 
            $qb->join('c.user', 'u');
        } elseif ($statut === 'Non') {
            // sans compte 
            $qb->leftJoin('c.user', 'u')
                ->andWhere('u IS NULL');
        }

        return $qb->getQuery()->getResult();
    }

    public function searchClientNoAccount(?string $surname, ?string $telephone): array
    {
        $qb = $this->createQueryBuilder('c');

        if (!empty($surname)) {
            $qb->andWhere('c.surname = :surname')
                ->setParameter('surname', $surname);
        }

        if (!empty($telephone)) {
            $qb->andWhere('c.telephone = :telephone')
                ->setParameter('telephone', $telephone);
        }

        return $qb->getQuery()->getResult();
    }



    /**
     * @return Paginator Returns an array of Dette objects
     */
    public function findPaginatedClients(int $page = 1, int $limit = 7): Paginator
    {
        $query = $this->createQueryBuilder('c')
            ->orderBy('c.id', 'ASC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery();

        $paginator = new Paginator($query);
        return $paginator;
    }



    /**
     * @return Paginator Returns an array of Dette objects
     */
    public function findClientsWithoutUser(int $page = 1, int $limit = 10): Paginator
    {
        $query = $this->createQueryBuilder('c')
            ->leftJoin('c.user', 'u') 
            ->where('u.id IS NULL') 
            ->orderBy('c.id', 'ASC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery();
    
        return new Paginator($query);
    }
    


 




    //    public function findOneBySomeField($value): ?Client
    //    {
    //        return $this->createQueryBuilder('c')
    //            ->andWhere('c.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
