<?php

namespace App\Repository;

use App\Entity\SubscriptionUser;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SubscriptionUser|null find($id, $lockMode = null, $lockVersion = null)
 * @method SubscriptionUser|null findOneBy(array $criteria, array $orderBy = null)
 * @method SubscriptionUser[]    findAll()
 * @method SubscriptionUser[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SubscriptionUserRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SubscriptionUser::class);
    }
}
