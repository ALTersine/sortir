<?php

namespace App\SortieService;

use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use Symfony\Component\Form\FormInterface;

class LieuManager
{
    public function createLieuFromSortie(FormInterface $form):Lieu{
        $newLieu = new Lieu();
        $newLieu->setName($form->get('lieuNom')->getData());
        $newLieu->setRue($form->get('lieuRue')->getData());
        $newLieu->setCodePostal($form->get('lieuCodePostal')->getData());
        $newLieu->setCoordonneesGps($form->get('lieuCoordonnees')->getData());
        return $newLieu;
    }

    public function setLieuInput(FormInterface $form, Sortie $sortie): void{
        foreach($sortie->getLieux() as $lieu){
            $form->get('lieuNom')->setData($lieu->getName());
            $form->get('lieuRue')->setData($lieu->getRue());
            $form->get('lieuCodePostal')->setData($lieu->getCodePostal());
            $form->get('lieuCoordonnees')->setData($lieu->getCoordonneesGps());
        }
    }

}
