<?php

namespace App\Controller;

use App\Entity\Etablissement;
use App\Entity\User;
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
    public function etablissements(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request): Response
    {
        $pagination = $paginator->paginate(
            $entityManager->getRepository(Etablissement::class)->findBy(['estActif' => 1], ["nom" => "ASC"]),
            $request->query->getInt('page', 1),
            9
        );

        return $this->render('etablissements/etablissements.html.twig', [
            'pagination' => $pagination
        ]);
    }

    #[Route('/etablissements/{slug}', name: 'app_etablissement')]
    public function etablissement(EntityManagerInterface $entityManager, $slug): Response
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

        $favoriToggler->favoriToggle($entityManager, $etablissement, $user);
        
        return $this->redirectToRoute('app_etablissement', ['slug' => $slug]);
    }

    #[Route('/etablissements/favoris', name: 'app_etablissements_favoris', priority: 1)]
    public function etablissementsFavoris(EntityManagerInterface $entityManager, PaginatorInterface $paginator, Request $request, Security $security,): Response
    {

        $favoris = $entityManager->getRepository(User::class)->findOneBy(["email" => $this->getUser()->getUserIdentifier()])->getFavoris();


        foreach ($favoris as $key => $value) {
            if (!$value->isEstActif()) {
                unset($favoris[$key]);
            }
        }

        $pagination = $paginator->paginate(
            $favoris,
            $request->query->getInt('page', 1),
            9
        );

        return $this->render('etablissements/etablissementsFavoris.html.twig', [
            'pagination' => $pagination
        ]);
    }
}
