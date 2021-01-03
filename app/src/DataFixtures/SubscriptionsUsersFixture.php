<?php

namespace App\DataFixtures;

use App\Entity\SubscriptionType;
use App\Entity\User;
use App\Exception\ReferenceNotFoundException;
use App\Service\Factory\UserSubscriptionsCreator;
use DateTime;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;


class SubscriptionsUsersFixture extends CoreFixture implements DependentFixtureInterface
{
    /**
     * @var UserSubscriptionsCreator
     */
    private UserSubscriptionsCreator $subscriptionsFactory;

    /**
     * SubscriptionsUsersFixture constructor.
     *
     * @param UserSubscriptionsCreator $userSubscriptionsManager
     */
    public function __construct(UserSubscriptionsCreator $userSubscriptionsManager) {
        $this->subscriptionsFactory =$userSubscriptionsManager;
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
            $entity = $this->subscriptionsFactory->createForUser($user, $subscriptionType);
            $entity->setActive(true);
            $entity->setActivateAt($this->faker->dateTimeBetween('-1 years','now'));
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
