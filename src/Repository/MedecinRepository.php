<?php

namespace App\Repository;

use App\Entity\Medecin;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Persistence\ManagerRegistry;

/**
 * @method Medecin|null find($id, $lockMode = null, $lockVersion = null)
 * @method Medecin|null findOneBy(array $criteria, array $orderBy = null)
 * @method Medecin[]    findAll()
 * @method Medecin[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class MedecinRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Medecin::class);
    }

    /**
     * @param $nom
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function medecinLikeNom($nom): \Doctrine\ORM\QueryBuilder
    {
        return $this->createQueryBuilder('m')
            ->where('m.nom like :nom or m.prenom like :nom')
            ->setParameter('nom', "$nom%")
            ->orderBy('m.nom');
    }

    /**
     * @param $matricule
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function medecinByMatricule($matricule)
    {
        return $this->createQueryBuilder('m')
            ->where('m.matricule = :matricule')
            ->setParameter('matricule', "$matricule%");
    }
}
