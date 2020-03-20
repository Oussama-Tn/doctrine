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
        $this->createPurchaseOrdersForSomeUsers($manager);
    }

    private function createPurchaseOrdersForSomeUsers(ObjectManager $manager)
    {
        $someUsers = $this->getSomeUsers();

        foreach ($someUsers as $user) {

            $randomNumber = rand(1, 3);

            for ($i = 0; $i < $randomNumber; $i++) {
                $purchaseOrder = new PurchaseOrder();
                $purchaseOrder->setUser($user);
                $purchaseOrder->setDate($this->faker->dateTimeBetween('-6 months', '-3 days'));
                $manager->persist($purchaseOrder);
            }
        }

        $manager->flush();
    }

    /**
     * We'll use this for example to see how many users doesn't have orders yet
     * @return array
     */
    private function getSomeUsers(): array
    {
        $users = $this->userRepository->findAll();

        $someUsers = [];

        foreach ($users as $k => $user) {
            if ($k > 2) {
                $someUsers[] = $user;
            }
        }

        return $someUsers;
    }

    public function getDependencies()
    {
        return [
            UserFixtures::class,
        ];
    }

}
