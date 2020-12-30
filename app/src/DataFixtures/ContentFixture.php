<?php

namespace App\DataFixtures;

use App\Entity\Content;
use App\Entity\SubscriptionType;
use App\Model\Id;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;


class ContentFixture extends CoreFixture implements DependentFixtureInterface
{
    public function loadData(ObjectManager $manager)
    {
        $count = 10;
        for ($i = 0; $i < $this->basicQuantityForGenerate; $i++) {
            $entity = new Content(
                Id::next(),
                $this->faker->name,
                $this->faker->numberBetween(1880, 2020),
            );
            /** @var SubscriptionType $SubscriptionType */
            $SubscriptionType = $this->getRandomReference(
                SubscriptionType::class
            );
            $entity->addSubscriptionType($SubscriptionType);
            $manager->persist($entity);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            SubscriptionsTypesFixture::class,
        );
    }
}
