<?php

namespace App\DataFixtures;


use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;



class User extends Fixture
{

    private Generator $faker;
    private UserPasswordHasherInterface $passwordHasher;


    public function __construct(UserPasswordHasherInterface $hasher, UserPasswordHasherInterface $passwordHasher)
    {
        $this->faker = Factory::create('fr_FR');
        $this->passwordHasher = $passwordHasher;
    }


    public function load(ObjectManager $objectManager): void
    {

        for ($i = 1;$i <= NOMBRE_DE_USER; $i++)
        {

            $user = new \App\Entity\User();

            $user->setEmail($this->faker->email())
                 ->setPassword($this->passwordHasher->hashPassword($user, "Bonjour25"))
                 ->setPrenom($this->faker->firstName())
                 ->setNom($this->faker->lastName())
                 ->setCreatedAt($this->faker->dateTimeBetween("-3weeks"))
                 ->setEstActif($this->faker->boolean(80));

            if ($this->faker->boolean(50)) {
                $user->setPseudo($this->faker->userName());
            }

            if ($this->faker->boolean(30)) {
                $user->setUpdatedAt($this->faker->dateTimeBetween($user->getCreatedAt()));
            }


            $user->setRoles(
                [
                    ROLES[array_rand(ROLES)]
                ]
            );


            $objectManager->persist($user);

        }


        $objectManager->flush();

    }
}
