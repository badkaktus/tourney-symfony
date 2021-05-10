<?php

namespace App\Repository;

use App\Entity\Tourney;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Tourney|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tourney|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tourney[]    findAll()
 * @method Tourney[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TourneyRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tourney::class);
    }

    public function getAllResultsInTourney(int $id)
    {
        $conn = $this->getEntityManager()->getConnection();
        $sql = '
            SELECT m.*,gr.group_letter,por.`round` FROM `matches` m
LEFT JOIN group_round gr ON gr.match_id = m.id
LEFT JOIN play_off_round por ON por.match_id = m.id
WHERE m.tourney_id = :tourney_id
            ';
        $stmt = $conn->prepare($sql);
        $stmt->executeQuery(['tourney_id' => $id]);
        return $stmt->fetchAll();
    }

    // /**
    //  * @return Tourney[] Returns an array of Tourney objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Tourney
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
