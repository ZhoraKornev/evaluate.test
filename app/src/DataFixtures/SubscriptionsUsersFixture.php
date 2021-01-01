<?php

namespace App\DataFixtures;

use App\Entity\Content;
use App\Entity\SubscriptionUser;
use App\Entity\User;
use App\Exception\ReferenceNotFoundException;
use App\Model\Id;
use App\Model\User\Email;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class SubscriptionsUsersFixture extends CoreFixture implements DependentFixtureInterface
{
    /**
     * @param ObjectManager $manager
     *
     * @throws ReferenceNotFoundException
     */
    public function loadData(ObjectManager $manager)
    {
        $output = new ConsoleOutput();


        $this->basicQuantityForGenerate = 3;
        for ($i = 0; $i < $this->basicQuantityForGenerate; $i++) {

            /** @var User $user */
            $user = $this->getRandomReference(
                User::class
            );
            /** @var Content $user */
            $content = $this->getRandomReference(
                Content::class
            );

            $user = new SubscriptionUser(Id::next(), $content, $user);
            $password = $this->faker->password(3,5);
            $passwordHash = $this->encoder->encodePassword($user, $password);
            $user->setPassword($passwordHash);
            if ($this->faker->boolean(80)){
                $user->setActive();
                $output->writeln("{$user->getEmail()->getValue()} is active NOW and have password = <info>$password</info>");
                $output->writeln("You can use this credentials for login");
            }
            $manager->persist($user);
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
