<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserFixtures extends Fixture
{
    private $passwordEncoder;
    /**
     * @var \Faker\Generator
     */
    private $faker;
    private $password = 'secret';
    private $usersNumber = 30;

    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->faker = Factory::create();
    }

    public function load(ObjectManager $manager)
    {
        $this->loadFakeUsers($manager, $this->usersNumber);
    }

    /**
     * @param ObjectManager $manager
     * @param int $number
     */
    private function loadFakeUsers(ObjectManager $manager, int $number): void
    {
        for ($i = 0; $i < $number; $i++):

            $user = new User();

            $firstName = $this->faker->firstName;
            $lastName = $this->faker->lastName;

            $user->setEmail($firstName . '.' . $lastName . '@doctrine.test');
            $user->setFirstName($firstName);
            $user->setLastName($lastName);
            $user->setRegisterDate($this->faker->dateTimeBetween('-3 years', '-1 years'));
            $password = $this->passwordEncoder
                ->encodePassword($user, $this->password);
            $user->setZipcode($this->faker->numberBetween(28000, 92000));
            $user->setPassword($password);
            $user->setBirthDate($this->faker->dateTimeBetween('-50 years', '-25 years'));
            $user->setCity($this->faker->city);
            $user->setPhoneNumber($this->faker->phoneNumber);

            $manager->persist($user);

        endfor;

        $manager->flush();
    }
}
