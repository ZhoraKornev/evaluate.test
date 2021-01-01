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
    public function loadData(ObjectManager $manager) {
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
            if ($this->faker->boolean(80)) {
                $iterationNumber = $this->faker->numberBetween(0, 10);
                for ($j = 0; $j < $iterationNumber; $j++) {
                    /** @var SubscriptionType $SubscriptionExtra */
                    $SubscriptionExtra = $this->getRandomReference(
                        SubscriptionType::class
                    );
                    $entity->addSubscriptionType($SubscriptionExtra);
                }
            }
            $manager->persist($entity);
        }
        $manager->flush();
    }

    public function getDependencies() {
        return array(
            SubscriptionsTypesFixture::class,
        );
    }
}
