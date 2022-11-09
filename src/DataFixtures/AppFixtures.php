<?php

namespace App\DataFixtures;
include "utils/consts/fixturesConsts.php";

use App\Entity\Categorie;
use App\Entity\Etablissement;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Faker\Factory;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\String\Slugger\SluggerInterface;

class AppFixtures extends Fixture
{

    private SluggerInterface $slugger;

    /**
     * @param SluggerInterface $slugger
     */
    public function __construct(SluggerInterface $slugger)
    {
        $this->slugger = $slugger;
    }


    public function load(ObjectManager $objectManager): void
    {

        $faker = Factory::create('fr_FR');

        for ($i = 1;$i <= NOMBRE_DE_ETABLISSEMENT; $i++)
        {

            $villes = $objectManager->getRepository(Ville::class)->findAll();
            shuffle($villes);
            $categories = $objectManager->getRepository(Categorie::class)->findAll();
            shuffle($categories);

            $etablissement = new Etablissement();


            $etablissement->setVille($villes[rand(1, NOMBRE_DE_VILLE - 1)])
                          ->setNom($faker->words(3, true))
                          ->setSlug($this->slugger->slug($etablissement->getNom()))
                          ->setDescription($faker->paragraph)
                          ->setNumeroTelephone($faker->phoneNumber)
                          ->setAdressePostale($faker->streetAddress . ', ' . $etablissement->getVille()->getCodePostal() . ', ' . $etablissement->getVille()->getNom())
                          ->setAdresseEmail($faker->email)
                          ->setEstActif($faker->boolean)
                          ->setEstSurAccueil($faker->boolean)
                          ->setCreatedAt($faker->dateTimeBetween("-1 week", "-3 day"))
                          ->setUpdatedAt($faker->dateTimeBetween("-1 day", "now"))
                          ->addCategorie($categories[rand(0, NOMBRE_DE_CATEGORIE - 1)]);

            $objectManager->persist($etablissement);

        }


        $objectManager->flush();

    }
}
