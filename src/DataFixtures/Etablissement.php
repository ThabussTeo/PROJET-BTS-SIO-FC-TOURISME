<?php

namespace App\DataFixtures;
include "utils/consts/etablissementConsts.php";

use App\Entity\Categorie;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Faker\Generator;
use Symfony\Component\String\Slugger\SluggerInterface;

class Etablissement extends Fixture
{

    private SluggerInterface $slugger;
    private Generator $faker;

    /**
     * @param SluggerInterface $slugger
     */
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
        $this->faker = Factory::create('fr_FR');
    }


    public function load(ObjectManager $objectManager): void
    {

        for ($i = 1;$i <= NOMBRE_DE_ETABLISSEMENT; $i++)
        {


            $villes = $objectManager->getRepository(Ville::class)->findAll();
            shuffle($villes);
            $categories = $objectManager->getRepository(Categorie::class)->findAll();
            shuffle($categories);

            $etablissement = new \App\Entity\Etablissement();


            $etablissement->setVille($villes[rand(1, NOMBRE_DE_VILLE - 1)])
                ->setNom($this->faker->words(3, true))
                ->setSlug($this->slugger->slug($etablissement->getNom()))
                ->setDescription($this->faker->paragraph())
                ->setNumeroTelephone($this->faker->phoneNumber())
                ->setAdressePostale($this->faker->streetAddress . ', ' . $etablissement->getVille()->getCodePostal() . ', ' . $etablissement->getVille()->getNom())
                ->setAdresseEmail($this->faker->email())
                ->setEstActif($this->faker->boolean())
                ->setEstSurAccueil($this->faker->boolean())
                ->setCreatedAt($this->faker->dateTimeBetween("-1 week", "-3 day"))
                ->setUpdatedAt($this->faker->dateTimeBetween("-1 day", "now"))
                ->addCategorie($categories[rand(0, NOMBRE_DE_CATEGORIE - 1)])
                ->addCategorie($categories[rand(0, NOMBRE_DE_CATEGORIE - 1)]);

            $objectManager->persist($etablissement);

        }


        $objectManager->flush();

    }
}
