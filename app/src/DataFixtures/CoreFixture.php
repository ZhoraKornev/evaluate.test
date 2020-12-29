<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use InvalidArgumentException;

abstract class CoreFixture extends Fixture
{
    /** @var ObjectManager */
    private $objectManager;

    /** @var Generator */
    protected $faker;

    /**
     * @var int
     */
    protected int $basicQuantityForGenerate = 10;

    /**
     * @var array
     */
    private array $referencesIndex = [];

    abstract protected function loadData(ObjectManager $manager);

    public function load(ObjectManager $manager)
    {
        $this->objectManager = $manager;
        $this->faker = Factory::create();
        $this->loadData($manager);
    }

    /**
     * @param string $className
     * @param int    $number
     *
     * @return string
     */
    protected function resolveReferenceName(string $className, int $number = 0): string
    {
        return $className . '_' . $number;
    }
}