<?php

namespace App\Repository;

use App\Entity\CompteMedecin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method CompteMedecin|null find($id, $lockMode = null, $lockVersion = null)
 * @method CompteMedecin|null findOneBy(array $criteria, array $orderBy = null)
 * @method CompteMedecin[]    findAll()
 * @method CompteMedecin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CompteMedecinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, CompteMedecin::class);
    }

    /*
    public function findOneBySomeField($value): ?CompteMedecin
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
