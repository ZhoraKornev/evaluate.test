<?php

namespace App\DataFixtures;

use App\Entity\SubscriptionType;
use App\Model\Id;
use Doctrine\Persistence\ObjectManager;

class SubscriptionsTypesFixture extends CoreFixture
{
    private static array $subscriptionNames = [
        'Basic',
        '/\OX',
        'Ultra',
        'Vip',
        'Lux',
        'Vip Ultra',
        'Dlia banditov',
    ];

    private static array $subscriptionPeriods = [
        7,
        10,
        14,
        21,
        31,
        180,
        365
    ];

    /**
     * @param ObjectManager $manager
     */
    public function loadData(ObjectManager $manager)
    {
        for ($i = 0; $i < $this->basicQuantityForGenerate; $i++) {
            $entity = new SubscriptionType(
                Id::next(),
                $this->faker->randomElement(SubscriptionsTypesFixture::$subscriptionNames),
                $this->faker->numberBetween(10, 99999),
                $this->faker->randomElement(SubscriptionsTypesFixture::$subscriptionPeriods),
            );
            $manager->persist($entity);
            $this->addReference($this->resolveReferenceName(SubscriptionType::class, $i), $entity);
        }
        $manager->flush();
    }
}
