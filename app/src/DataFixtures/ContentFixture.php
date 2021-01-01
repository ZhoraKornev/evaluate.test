<?php

namespace App\DataFixtures;

use App\Entity\Content;
use App\Entity\SubscriptionType;
use App\Exception\ReferenceNotFoundException;
use App\Model\Id;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;


class ContentFixture extends CoreFixture implements DependentFixtureInterface
{
    /**
     * @param ObjectManager $manager
     *
     * @throws ReferenceNotFoundException
     */
    public function loadData(ObjectManager $manager)
    {
        for ($i = 0; $i < $this->basicQuantityForGenerate; $i++) {
            $entity = new Content(
                Id::next(),
                $this->faker->name,
                $this->faker->numberBetween(1880, 2020),
            );
            $entity->setDescription($this->faker->realText(300));
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
