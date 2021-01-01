<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Exception\ReferenceNotFoundException;
use App\Model\Id;
use App\Model\User\Email;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UsersFixture extends CoreFixture implements DependentFixtureInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    private $encoder;

    /**
     * UsersFixture constructor.
     *
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
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
            $user = new User(Id::next(),new Email($this->faker->email));
            $password = $this->faker->password(3,5);
            $passwordHash = $this->encoder->encodePassword($user, $password);
            $user->setPassword($passwordHash);
            if ($this->faker->boolean(80)){
                $user->setActive();
                $output->writeln("<info>{$user->getEmail()->getValue()}</info> is active NOW and have password = <info>$password</info>");
                $output->writeln("You can use this credentials for login");
            }
            $this->addReference(
                $this->resolveReferenceName(User::class, $i),
                $user
            );
            $manager->persist($user);
        }
        $manager->flush();
        $users = $manager->getRepository(User::class);
        if (!$users->count(['status' => User::STATUS_ACTIVE])){
            $output->writeln("<error>Rerun seeding DB NO active user for test</error>");
        }
    }

    public function getDependencies()
    {
        return array(
            SubscriptionsTypesFixture::class,
            ContentFixture::class,
        );
    }
}
