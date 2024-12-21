<?php

namespace App\Repository;

use App\Entity\Paiement;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Paiement>
 */
class PaiementRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Paiement::class);
    }

            /**
         * @return Paginator Returns an array of Dette objects
         */
        public function findByDette($idDette, int $page=1, int $limit=7): Paginator
        {
            $query=$this->createQueryBuilder('p')
                ->andWhere('p.dette = :val')
                ->setParameter('val', $idDette)
                ->orderBy('p.id', 'ASC')
                ->setFirstResult(($page-1)*$limit)
                ->setMaxResults($limit)
                ->getQuery();

            $paginator = new Paginator($query);
            return $paginator;
            
        }

            /**
     * @return Paginator Returns an array of Dette objects
     */
    public function findPaginatedPaiements(int $page = 1, int $limit = 7): Paginator
    {
        $query = $this->createQueryBuilder('c')
            ->orderBy('c.id', 'ASC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery();

        $paginator = new Paginator($query);
        return $paginator;
    }


    public function searchPaiements(?string $surname): array
    {
        $qb = $this->createQueryBuilder('p')
            ->join('p.dette', 'd') // Jointure avec la dette
            ->join('d.client', 'c') // Jointure avec le client via la dette
            ->orderBy('p.id', 'ASC');
    
        if (!empty($surname)) {
            $qb->andWhere('c.surname = :surname')
                ->setParameter('surname', $surname);
        }
    
        return $qb->getQuery()->getResult();
    }
    

    //    /**
    //     * @return Paiement[] Returns an array of Paiement objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('p.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?Paiement
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
