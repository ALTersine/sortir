<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Enum\EtatSortie;
use App\Form\SortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\SortieService\EtatManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/Sortie')]
final class SortieController extends AbstractController
{
    #[Route('/', name: 'sortie_liste', methods: ['GET'])]
    public function list(Request $request, SortieRepository $sortieRepository): Response
    {
        //va chercher les sorties dans la bdd
        $page = max(1, (int) $request->query->get('page', 1));
        $limit = 10;

        $paginator = $sortieRepository->findLastEvents($page, $limit);

        $total = count($paginator);
        $pages = (int) ceil($total / $limit);

        return $this->render('sortie/list.html.twig', [
            //passe les sorties à twig pour affichage
            'sorties' => $paginator,
            'page' => $page,
            'pages' => $pages,
            'total' => $total,
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
    public function create(
        Request $request,
        EntityManagerInterface $em,
        EtatManager $serviceEtat,
        Security $security,
    ): Response
    {
        $newSortie = new Sortie();
        $newLieu = new Lieu();
        $form = $this->createForm(SortieType::class, $newSortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newLieu->setName($form->get('lieuNom')->getData());
            $newLieu->setRue($form->get('lieuRue')->getData());
            $newLieu->setCodePostal($form->get('lieuCodePostal')->getData());
            $newLieu->setCoordonneesGps($form->get('lieuCoordonnees')->getData());

            $em->persist($newSortie);

            $newSortie->addLieux($newLieu);

            $newSortie->setOrganisateur($security->getUser());

            if($form->get('publier')->isClicked()) {
                $newSortie->setEtat($serviceEtat->getRightEtat(EtatSortie::OUVERTE));
                $newSortie->setPublished(true);
            }else{
                $newSortie->setEtat($serviceEtat->getRightEtat(EtatSortie::EN_CREATION));
            }
            $em->persist($newLieu);

            $em->flush();

            $this->addFlash('success','La sortie est prête ! Découvrez en tous les détails ici');
            return $this->redirectToRoute('sortie_show', ['id' => $newSortie->getId()]);
        }

        return $this->render('sortie/create.html.twig',[
            'form' => $form,
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
