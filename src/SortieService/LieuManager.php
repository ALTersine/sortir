<?php

namespace App\SortieService;

use App\Entity\Lieu;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Exception\LieuNotFound;
use App\Repository\LieuRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Exception\CannotCreateTag;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

class LieuManager
{
    public function __construct(
        private readonly LieuRepository $repo,
        private readonly EntityManagerInterface $em
    )
    {
    }

    public function createLieuFromJSON(bool $isCreatingSortie, Request $request, Sortie $sortie, FormInterface $form, EntityManagerInterface $em) : void {
        $JSON = $request->request->get('lieux_choosen');
        if($JSON){
            $allLieux = json_decode($JSON, true);

            foreach ($allLieux as $lieu) {
                $lieu = new Lieu()
                    ->setName($lieu['name'])
                    ->setRue($lieu['address'])
                    ->setLatitude($lieu['latitude'])
                    ->setLongitude($lieu['longitude'])
                    ->setCoordonneesGps($lieu['latitude']. ' / '. $lieu['longitude'])
                    ->setVille($form->get('lieuVille')->getData());

                $sortie->addLieux($lieu);
                $em->persist($lieu);
            }
        }elseif ($isCreatingSortie){
            throw new CannotCreateTag('Aucun lieu attribué à cette sortie');
        }

    }

    public function deleteLieu(int $id) : void{
        $lieu = $this->repo->find($id);
        if(!$lieu){
            throw new LieuNotFound('Erreur à la récupération du lieu.');
        }
        $this->em->remove($lieu);
        $this->em->flush();
    }
}
