<?php

namespace App\Controller;

use App\Entity\Etablissement;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EtablissementController extends AbstractController
{
    #[Route('/etablissements', name: 'app_etablissements')]
    public function etablissement(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {

        $pagination = $paginator->paginate(
            $entityManager->getRepository(Etablissement::class)->findBy(['estActif' => 1], ["nom" => "ASC"]),
            $request->query->getInt('page', 1), /*page number*/
            9
        );

        return $this->render('etablissements/index.html.twig', [
            'pagination' => $pagination
        ]);
    }

    #[Route('/etablissements/{id}', name: 'app_etablissement')]
    public function etablissements(EntityManagerInterface $entityManager, $id): Response
    {


        $etablissement = $entityManager->getRepository(Etablissement::class)->find($id);

        return $this->render('etablissements/etablissement.html.twig', [
            'etablissement' => $etablissement
        ]);
    }
}
