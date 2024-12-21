<?php

namespace App\Repository;

use App\Entity\Dette;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Query\Limit;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Dette>
 */
class DetteRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Dette::class);
    }

    /**
     * @return Paginator Returns an array of Dette objects
     */
    public function findByClient(int $clientId, int $page, int $limit): Paginator
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
    


    /**
     * @return Paginator Returns an array of Dette objects
     */
    public function findAllDettes(int $page = 1, int $limit = 7): Paginator
    {
        $query = $this->createQueryBuilder('d')
            ->leftJoin('d.articles', 'a') // Jointure gauche pour inclure toutes les dettes
            ->addSelect('a')
            ->orderBy('d.id', 'ASC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery();

        return new Paginator($query);
    }




    public function searchDettes(?string $surname, ?string $telephone, ?string $statut, int $page, int $limit = 9): Paginator
    {
        $qb = $this->createQueryBuilder('d')
            ->join('d.articles', 'a') // Jointure avec la table Article
            ->join('d.client', 'c') // Jointure avec Client pour le nom et le téléphone
            ->orderBy('d.id', 'ASC');

        if (!empty($surname)) {
            $qb->andWhere('c.surname = :surname')
                ->setParameter('surname', $surname);
        }

        if (!empty($telephone)) {
            $qb->andWhere('c.telephone = :telephone')
                ->setParameter('telephone', $telephone);
        }

        if ($statut === 'Soldées') {
            $qb->andWhere('d.montant = d.montantVerser');
        } elseif ($statut === 'Non Soldées') {
            $qb->andWhere('d.montant != d.montantVerser');
        }

        $query = $qb->getQuery();
        $query->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        return new Paginator($query);
    }



    public function findSoldedDebts(int $page = 1, int $limit = 10): Paginator
    {
        $query = $this->createQueryBuilder('d')
            ->where('d.montant = d.montantVerser') // Critère pour les dettes soldées
            ->andWhere('d.archived = false') // Exclure les dettes archivées
            ->orderBy('d.id', 'DESC')
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit)
            ->getQuery();

        return new Paginator($query);
    }




    //    public function findOneBySomeField($value): ?Dette
    //    {
    //        return $this->createQueryBuilder('d')
    //            ->andWhere('d.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
