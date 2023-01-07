<?php

namespace App\Service;

use App\Entity\Etablissement;
use Symfony\Component\Security\Core\Security;
use Doctrine\ORM\EntityManagerInterface;

class FavoriToggler {

    public function favoriToggle($entityManager, $etablissement, $user)
    {
        
        if ($user->getFavoris()->contains($etablissement)) {
            $user->removeFavori($etablissement);
        } else {
            $user->addFavori($etablissement);
        }

        $entityManager->persist($user);
        $entityManager->flush();

    }
}