<?php

namespace App\Repository;

use App\Entity\Category;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class CategoryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Category::class);
    }

    public function findAllWithFeatures(): array
    {
        return $this->createQueryBuilder('c')
            ->leftJoin('c.features', 'f')
            ->leftJoin('f.tags', 't')
            ->leftJoin('f.examples', 'e')
            ->addSelect('f', 't', 'e')
            ->orderBy('c.name', 'ASC')
            ->addOrderBy('f.name', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
