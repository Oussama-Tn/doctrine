<?php

namespace App\DataFixtures;

use App\Entity\PurchaseOrder;
use App\Repository\UserRepository;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;

class PurchaseOrderFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var Factory
     */
    private $faker;

    /**
     * PurchaseOrderFixtures constructor.
     * @param UserRepository $userRepository
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $this->createPurchaseOrdersForAllUsers($manager);
    }

    private function createPurchaseOrdersForAllUsers(ObjectManager $manager)
    {
        $users = $this->userRepository->findAll();

        foreach ($users as $user) {

            $randomNumber = rand(2, 7);

            for ($i = 0; $i < $randomNumber; $i++) {
                $purchaseOrder = new PurchaseOrder();
                $purchaseOrder->setUser($user);
                $purchaseOrder->setDate($this->faker->dateTimeBetween('-6 months', '-3 days'));
                $manager->persist($purchaseOrder);
            }
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }

}
