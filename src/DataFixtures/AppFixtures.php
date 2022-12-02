<?php

namespace App\DataFixtures;


use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        /* $faker = Factory::create();

        for ($i = 0; $i < 3; $i++) {
            $user = new User($this->passwordHasher);
            $user->setUsername($faker->username())->setPassword('password');
            $manager->persist($user);
        } */


        $user = new User($this->passwordHasher);
        $user->setUsername("sinthy")->setPassword("password")->setRoles(["ROLE_ADMIN"]);
        $manager->persist($user);

        $user = new User($this->passwordHasher);
        $user->setUsername("chris")->setPassword("333")/* ->setRoles("ROLE_ISAUTHENTICATION") */;
        $manager->persist($user);

        $manager->flush();
    }
}