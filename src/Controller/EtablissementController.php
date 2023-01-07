<?php

namespace App\Controller;

use App\Entity\Etablissement;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use App\Service\FavoriToggler;

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

    #[Route('/etablissements/{slug}', name: 'app_etablissement')]
    public function etablissements(EntityManagerInterface $entityManager, $slug): Response
    {


        $etablissement = $entityManager->getRepository(Etablissement::class)->findOneBy(["slug" => $slug]);

        return $this->render('etablissements/etablissement.html.twig', [
            'etablissement' => $etablissement
        ]);
    }


    #[Route('/etablissements/favoris/{slug}', name: 'app_favori_toggle')]
    public function favorisToggle(FavoriToggler $favoriToggler, EntityManagerInterface $entityManager, Security $security, $slug): Response
    {

        $etablissement = $entityManager->getRepository(Etablissement::class)->findOneBy(["slug" => $slug]);
        $user = $security->getUser();

        $favoriToggler->favoriToggle($entityManager, $etablissement, $user, $slug);
        
        return $this->redirectToRoute('app_etablissement', ['slug' => $slug]);
    }

}
