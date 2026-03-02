<?php

namespace App\Controller;

use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/sortie')]
final class SortieController extends AbstractController
{
    #[Route('/', name: 'sortie_liste', methods: ['GET'])]
    public function list(SortieRepository $sortieRepository): Response
    {
        //va chercher les sorties dans la bdd
        $sorties = $sortieRepository->findAll();

        return $this->render('sortie/list.html.twig', [
            //passe les sorties à twig pour affichage
            'sorties' => $sorties,
        ]);
    }

    #[Route('/{id}', name: 'sortie_show', requirements: ['id'=>'\d+'], methods: ['GET'])]
    public function show($id, SortieRepository $sortieRepository): Response
    {
        //va chercher la sotie dans la bdd en fonction de l'id
        $sortie = $sortieRepository ->find($id);

        return $this->render('sortie/show.html.twig',[
            //passe la sortie à twig pour affichage
            'sortie' => $sortie,
        ]);
    }

    #[Route('/creer', name: 'sortie_create', methods: ['GET', 'POST'])]
    public function create(): Response
    {
        //todo: traiter le formulaire d'ajout de sortie

        return $this->render('sortie/create.html.twig',[
            //todo:passer le formulaire à twig
        ]);
    }

    #[Route('/{id}/modifier', name: 'sortie_edit', requirements: ['id'=>'\d+'], methods: ['GET', 'POST'])]
    public function edit($id): Response
    {
        //todo:aller chercher la sortie à modifier dans la bdd

        //todo: traiter le formulaire de modification de sortie

        return $this->render('sortie/edit.html.twig',[
            //todo:passer le formulaire à twig
        ]);
    }
}
