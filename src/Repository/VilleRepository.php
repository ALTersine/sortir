<?php

namespace App\Repository;

use App\Entity\Campus;
use App\Entity\Ville;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * @extends ServiceEntityRepository<Ville>
 */
class VilleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ville::class);
    }

    public function findAllVillesDesc()
    {
        return $this->createQueryBuilder('v')
            ->addSelect('c')
            ->leftJoin('v.campus', 'c')
            ->orderBy('v.id', 'DESC')
            ->getQuery()
            ->getResult();
    }
    public function findVilleWithFilters(int $idCampus, string $filterName, string $filterCodePostal): array
    {
        $query = $this->createQueryBuilder('v');

        if ($idCampus > 0) {
            $query
                ->addSelect('c')
                ->leftJoin('v.campus', 'c')
                ->andWhere('c.id = :campus')
                ->setParameter('campus', $idCampus);
        }

        if ($filterName) {
            $query
                ->andWhere('v.name LIKE :name')
                ->setParameter('name', '%' . $filterName . '%');
        }

        if ($filterCodePostal) {
            $query
                ->andWhere('v.codePostal LIKE :codePostal')
                ->setParameter('codePostal', '%' . $filterCodePostal . '%');
        }

        return $query->getQuery()->getResult();
    }

    public function villeLinkedToOneCampus(Campus $campus):array{
        return $this->createQueryBuilder('v')
            ->addSelect('c')
            ->leftJoin('v.campus', 'c')
            ->Where('v.campus = :campus')
            ->setParameter('campus', $campus)
            ->getQuery()
            ->getResult();
    }
}
