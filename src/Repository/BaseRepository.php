<?php

namespace App\Repository;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

abstract class BaseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry, $entityClass) {
        parent::__construct($registry, $entityClass);
    }

    public function findInRandomOrder(int $number = 1)
    {
        $ids = $this->randomIds($number);

        return $this->findBy(['id' => $ids]);
    }

    public function randomIds(int $number)
    {
        $elements = $this->createQueryBuilder('e')
            ->addSelect('e.id')
            ->getQuery()
            ->getArrayResult();

        $ids = [];

        foreach ($elements as $element) {
            $ids[] = $element[0]['id'];
        }

        if (empty($ids)) {
            return [];
        }

        $number = count($ids) > $number ? $number : count($ids);

        $keys = array_rand($ids, $number);
        $results = [];

        foreach ($keys as $key) {
            $results[] = $ids[$key];
        }

        return $results;
    }
}
