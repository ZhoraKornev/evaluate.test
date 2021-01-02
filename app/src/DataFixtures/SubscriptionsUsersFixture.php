<?php

namespace App\DataFixtures;

use App\Entity\SubscriptionType;
use App\Entity\User;
use App\Exception\ReferenceNotFoundException;
use App\Service\Factory\UserSubscriptionsCreator;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;


class SubscriptionsUsersFixture extends CoreFixture implements DependentFixtureInterface
{
    /**
     * @var UserSubscriptionsCreator
     */
    private UserSubscriptionsCreator $subscriptionsManager;

    /**
     * SubscriptionsUsersFixture constructor.
     *
     * @param UserSubscriptionsCreator $userSubscriptionsManager
     */
    public function __construct(UserSubscriptionsCreator $userSubscriptionsManager) {
        $this->subscriptionsManager =$userSubscriptionsManager;
    }


    /**
     * @param ObjectManager $manager
     *
     * @throws ReferenceNotFoundException
     */
    public function loadData(ObjectManager $manager)
    {
        $this->basicQuantityForGenerate = 3;
        for ($i = 0; $i < $this->basicQuantityForGenerate; $i++) {
            /** @var User $user */
            $user = $this->getRandomReference(
                User::class
            );
            /** @var SubscriptionType $subscriptionType */
            $subscriptionType = $this->getRandomReference(
                SubscriptionType::class
            );
            $entity = $this->subscriptionsManager->createForUser($user, $subscriptionType);
            $entity->activate();
            $manager->persist($entity);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            SubscriptionsTypesFixture::class,
            ContentFixture::class,
            UsersFixture::class,
        );
    }
}
