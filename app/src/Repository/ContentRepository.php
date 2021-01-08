<?php

namespace App\Repository;

use App\Entity\Content;
use App\Entity\SubscriptionType;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Content|null find($id, $lockMode = null, $lockVersion = null)
 * @method Content|null findOneBy(array $criteria, array $orderBy = null)
 * @method Content[]    findAll()
 * @method Content[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Content::class);
    }

    /**
     * @param SubscriptionType $subscriptionType
     *
     * @return Query
     */
    public function createQueryBuilderForPagination(SubscriptionType $subscriptionType) {
        return $this->createQueryBuilder('us')
//            ->leftJoin(SubscriptionType::class, 'st', Query\Expr\Join::ON)
//            ->where('st.id = :subscriptionTypeId')
            ->where(':subscriptionTypeId MEMBER OF us.subscriptionTypes')
            ->setParameter('subscriptionTypeId', $subscriptionType->getId())
            ->getQuery();

    }
}
