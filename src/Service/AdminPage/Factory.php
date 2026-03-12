<?php

namespace App\Service\AdminPage;

use App\Entity\Administrable;
use App\Entity\Campus;
use App\Entity\Ville;
use App\Repository\LieuRepository;
use App\Repository\VilleRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\Request;

class Factory
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly LieuRepository         $lieuRepo,
        private readonly VilleRepository        $villeRepo,
    )
    {
    }

    public function sendToBDDAndUpdateSessionList(Administrable $entity, array $lstSession, Request $request, Filters $filterService): void
    {
        $this->em->persist($entity);
        $this->em->flush();

        $lstSession[$entity->getId()] = $entity;

        $request->getSession()->set($filterService->getNomListSession(), $lstSession);
    }

    public function foundEntity(string $inputIdKey, ServiceEntityRepository $repo, Request $request): Administrable
    {
        $id = $request->request->get($inputIdKey);
        $entity = $repo->find($id);

        if (!$entity) {
            throw new EntityNotFoundException('Erreur à l\'identificaiton.');
        }

        return $entity;
    }

    public function deletingVille(Ville $ville, array $lstSession, Request $request, Filters $filterService): void
    {
        $id = $ville->getId();
        if ($this->lieuRepo->canVilleBeDeleted($ville)) {
            $this->em->remove($ville);
            $this->em->flush();

            unset($lstSession[$id]);
            $request->getSession()->set($filterService->getNomListSession(), $lstSession);

        } else {
            throw new \Exception('La ville concernée est utilisée pour une ou des sorties.', 403);
        }
    }

    public function deletingCampus(Request $request, Campus $campus, array $lstSessionCampus, Filters $filterServiceCampus, ?array $lstSessionVille, Filters $filterServiceVille): void
    {
        //ToDo : la sécurité ne marche pas à creuser

        $listeVilleATester = $this->villeRepo->villeLinkedToOneCampus($campus);
        //Vérification que l'on puisse bien aller sur jusqu'au bout avant de lancer la suppresionn ville puis campus.
        foreach ($listeVilleATester as $ville) {
            if (!$this->lieuRepo->canVilleBeDeleted($ville)) {
                throw new \Exception(
                    'Le campus est rattaché à la ville ' . $ville->getName() .
                    '. Etant utilisé dans une où des sorties, il n\'est pas possible de supprimer ce campus.',
                    403
                );
            }
        }

        //Si aucune exception, on lance le processus de suppresion de la ville.
        foreach ($listeVilleATester as $ville) {
            $id = $ville->getId();
            $this->em->remove($ville);
            if($lstSessionVille){
                unset($lstSessionVille[$id]);
            }
        }
        $id = $campus->getId();
        $this->em->remove($campus);
        unset($lstSessionCampus[$id]);

        //Suppresion en base
        $this->em->flush();

        //Réinjection des listes à jour en session
        $session = $request->getSession();
        if($lstSessionVille){
            $session->set($filterServiceVille->getNomListSession(), $lstSessionVille);
        }
        $session->set($filterServiceCampus->getNomListSession(), $lstSessionCampus);
    }

}
