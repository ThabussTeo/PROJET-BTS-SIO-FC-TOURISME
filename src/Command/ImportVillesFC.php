<?php

namespace App\Command;

use App\Entity\Ville;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use League\Csv\Reader;

#[AsCommand(
    name: 'app:import-villes-franche-comte',
    description: 'Importer les villes de Franche Comte',
    hidden: false,
    aliases: ['app:import-villes']
)]
class ImportVillesFC extends Command
{

    private EntityManagerInterface $manager;

    public function __construct(EntityManagerInterface $manager)
    {
        parent::__construct();
        $this->manager = $manager;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $departements = ["25", "39", "70", "90"];

        $csv = Reader::createFromPath('utils/villes.csv')
            ->setHeaderOffset(0)
            ->setDelimiter(';');

        foreach ($csv as $record)
        {
            if (in_array($record["Département"], $departements))
            {
                $ancienneCommune = "";
                $ville = new Ville();
                $ville->setCodePostal($record["Code postal"]);
                if (!empty($record["Ancienne commune"]))
                {
                    $ancienneCommune = "-" . $record["Ancienne commune"];
                }
                $ville->setNom($record["Commune"].$ancienneCommune)
                    ->setNomDepartement($record["Nom département"])
                    ->setNomRegion(($record["Région"]))
                    ->setNumeroDepartement($record["Département"]);

                $this->manager->persist($ville);
            }
        }

        $this->manager->flush();

        return Command::SUCCESS;
    }

}