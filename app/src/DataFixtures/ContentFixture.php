<?php

namespace App\DataFixtures;

use App\Entity\Content;
use App\Entity\SubscriptionType;
use App\Entity\User;
use App\Model\Id;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class ContentFixture extends CoreFixture
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    // ...
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
            $SubscriptionType = $this->getReference(
                $this->resolveReferenceName(
                    SubscriptionType::class,
                    $this->faker->numberBetween(0, $this->basicQuantityForGenerate)
                )
            );
            $entity->addSubscriptionType($SubscriptionType);
            $manager->persist($entity);
        }
        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            SubscriptionType::class,
        );
    }
}
