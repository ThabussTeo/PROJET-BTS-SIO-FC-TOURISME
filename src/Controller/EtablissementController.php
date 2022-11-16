<?php

namespace App\Controller;

use App\Entity\Etablissement;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EtablissementController extends AbstractController
{
    #[Route('/etablissement', name: 'app_etablissement')]
    public function index(EntityManagerInterface $entityManager): Response
    {

        // TODO Remmetre dans un service

        $etablissements = $entityManager->getRepository(Etablissement::class)->findBy(['estActif' => 1], ["nom" => "ASC"]);





        return $this->render('etablissement/index.html.twig', [
            'etablissements' => $etablissements,
        ]);
    }
}
