<?php

namespace App\SortieService;

use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Exception\LieuNotFound;
use Symfony\Component\Form\FormInterface;

class LieuManager
{
    public function createLieuFromSortie(FormInterface $form):Lieu{
        $newLieu = new Lieu();
        $newLieu->setName($form->get('lieuNom')->getData());
        $newLieu->setRue($form->get('lieuRue')->getData());
        $newLieu->setVille($form->get('lieuVille')->getData());
        $newLieu->setCoordonneesGps($form->get('lieuCoordonnees')->getData());
        return $newLieu;
    }

    public function setLieuInput(FormInterface $form, Sortie $sortie): void{
        if($sortie->getLieux()->count() <= 0){
            throw new LieuNotFound();
        }
        foreach($sortie->getLieux() as $lieu){
            $form->get('lieuNom')->setData($lieu->getName());
            $form->get('lieuRue')->setData($lieu->getRue());
            $form->get('lieuVille')->setData($lieu->getVille());
            $form->get('lieuCoordonnees')->setData($lieu->getCoordonneesGps());
        }
    }

    public function ctrlAndReplaceLieuData(Lieu $lieu, FormInterface $form) : void{
        if($lieu->getName() !== $form->get('lieuNom')->getData()){
            $lieu->setName($form->get('lieuNom')->getData());
        }
        if($lieu->getRue() !== $form->get('lieuRue')->getData()){
            $lieu->setRue($form->get('lieuRue')->getData());
        }
        if($lieu->getVille() !== $form->get('lieuVille')->getData()){
            $lieu->setVille($form->get('lieuVille')->getData());
        }
        if($lieu->getCoordonneesGps() !== $form->get('lieuCoordonnees')->getData()){
            $lieu->setCoordonneesGps($form->get('lieuCoordonnees')->getData());
        }
    }

}
