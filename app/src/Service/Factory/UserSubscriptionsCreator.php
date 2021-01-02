<?php


namespace App\Service\Factory;


use App\Entity\SubscriptionType;
use App\Entity\SubscriptionUser;
use App\Entity\User;
use App\Model\Id;
use DateTime;

class UserSubscriptionsCreator
{
    /**
     * @param User             $user
     * @param SubscriptionType $subscriptionType
     *
     * @return SubscriptionUser
     */
    public function createForUser(User $user, SubscriptionType $subscriptionType):SubscriptionUser {
        $entity = new SubscriptionUser(Id::next(), $subscriptionType, $user);
        $entity->setValidDue((new DateTime())->modify("+{$subscriptionType->getPeriod()} days"));
        return $entity;
    }
}
