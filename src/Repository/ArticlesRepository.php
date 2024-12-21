<?php

namespace App\Repository;

use App\Entity\Articles;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;


/**
 * @extends ServiceEntityRepository<Articles>
 */
class ArticlesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Articles::class);
    }

    //    /**
    //     * @return Articles[] Returns an array of Articles objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('a.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }


            /**
     * @return Paginator Returns an array of Dette objects
     */
    public function findPaginatedArticles(int $page = 1, int $limit = 7): Paginator
    {
        $query = $this->createQueryBuilder('u')
            ->orderBy('u.id', 'ASC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery();

        $paginator = new Paginator($query);
        return $paginator;
    }



    public function searchArticles(?string $libelle, ?string $etat, int $page, int $limit = 10): Paginator
    {
        $qb = $this->createQueryBuilder('d')
            ->orderBy('d.id', 'ASC');

        if (!empty($libelle)) {
            $qb->andWhere('d.libelle = :libelle')
                ->setParameter('libelle', $libelle);
        }

        if ($etat === 'Disponible') {
            $qb->andWhere('d.qteStock != 0');
        } elseif ($etat === 'En Rupture') {
            $qb->andWhere('d.qteStock = 0');
        }

        $query = $qb->getQuery();
        $query->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return new Paginator($query);
    }



    //    public function findOneBySomeField($value): ?Articles
    //    {
    //        return $this->createQueryBuilder('a')
    //            ->andWhere('a.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
