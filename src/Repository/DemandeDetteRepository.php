<?php

namespace App\Repository;

use App\Entity\DemandeDette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<DemandeDette>
 */
class DemandeDetteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, DemandeDette::class);
    }

    //    /**
    //     * @return DemandeDette[] Returns an array of DemandeDette objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('d.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }


        /**
     * @return Paginator Returns an array of Dette objects
     */
    public function findPaginatedDemandeDette(int $page = 1, int $limit = 7): Paginator
    {
        $query = $this->createQueryBuilder('c')
            ->orderBy('c.id', 'ASC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery();

        $paginator = new Paginator($query);
        return $paginator;
    }



    public function searchDemande(?string $surname): array
    {
        $qb = $this->createQueryBuilder('d') // 'd' pour DemandeDette
            ->join('d.client', 'c') // Jointure avec la table Client
            ->orderBy('d.id', 'ASC');
    
        if (!empty($surname)) {
            $qb->andWhere('c.surname = :surname')
                ->setParameter('surname', $surname);
        }
    
        return $qb->getQuery()->getResult();
    }


        /**
     * @return Paginator Returns an array of Dette objects
     */
    public function findByClientDemandes(int $clientId, int $page, int $limit): Paginator
    {
        $qb = $this->createQueryBuilder('d')
            ->join('d.client', 'c')
            ->where('c.id = :clientId')
            ->setParameter('clientId', $clientId)
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->orderBy('d.dateAt', 'DESC');
    
        return new Paginator($qb->getQuery());
    }
    

    //    public function findOneBySomeField($value): ?DemandeDette
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
