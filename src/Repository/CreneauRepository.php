<?php

namespace App\Repository;

use App\Entity\Creneau;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Creneau|null find($id, $lockMode = null, $lockVersion = null)
 * @method Creneau|null findOneBy(array $criteria, array $orderBy = null)
 * @method Creneau[]    findAll()
 * @method Creneau[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class CreneauRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Creneau::class);
    }

    /**
     * @param $hd
     * @param $idm
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function creneauInIntervalle($hd, $idm): \Doctrine\ORM\QueryBuilder
    {
        return $this->createQueryBuilder('c')
            ->innerJoin('c.medecin', 'm')
            ->where('c.heure_debut < :hd and c.heure_fin > :hd and m.id = :id')
            ->setParameter('id', $idm)
            ->setParameter('hd', $hd);
    }
}
