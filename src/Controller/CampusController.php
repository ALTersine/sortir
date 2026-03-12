<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Repository\CampusRepository;
use App\Util\AdminPage\Factory;
use App\Util\AdminPage\Filters;
use App\Util\AdminPage\MadeForCampus;
use App\Util\AdminPage\MadeForVille;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/campus', name: 'app_campus')]
#[IsGranted('ROLE_ADMIN')]
final class CampusController extends AbstractController
{
    #[Route('/admin', name: '_admin')]
    public function index(
        Request $request,
        Factory $service,
        MadeForCampus $filters,
        MadeForVille $filtreVille,
        CampusRepository $repo,
    ): Response
    {
        //Initialisation de ma page
        $lstCampus = $filters->initCampusList($request);
        $nameFiltered = $filters->initCampusName($request);

        /*************************** Partie Create **************************/
        //Initialisation des éléments du create
        $newCampus = new Campus();
        $formCreate = $this->createForm(CampusType::class, $newCampus);
        $formCreate->handleRequest($request);

        //Gestion de la création d'un campus
        if($formCreate->isSubmitted() && $formCreate->isValid()){
            $service->sendToBDDAndUpdateSessionList($newCampus, $lstCampus, $request, $filters);

            $this->addFlash('success','Votre campus est créé. N\'oubliez pas d\'y ajouter vos participants pour qu\'ils puissent créer des sorties.');
            return $this->redirectToRoute('app_campus_admin');
        }

        /*************************** Partie Update **************************/
        if ($request->request->has('campus_edit_id')) {
            //Campus que l'on veut modifier
            try {
                $campus = $service->foundEntity('campus_edit_id', $repo, $request);
            } catch (\Exception $e) {
                $this->addFlash('danger', $e->getMessage() . ' Le campus n\'existe pas.');
                return $this->redirectToRoute('app_campus_admin');
            }

            //Nouvelle donnée du campus
            $campus->setName($request->request->get('campus_name'));

            //Update de campus
            $service->sendToBDDAndUpdateSessionList($campus, $lstCampus, $request, $filters);

            $this->addFlash('success', 'Le campus a été mise à jour');
            return $this->redirectToRoute('app_campus_admin');
        }

        /*************************** Partie Delete **************************/
        if ($request->request->has('campus_delete_id')) {
            try {
                $campus = $service->foundEntity('campus_delete_id', $repo, $request);
                try {
                    $service->deletingCampus(
                        $request,
                        $campus,
                        $lstCampus,
                        $filters,
                        $request->request->get($filtreVille->getNomListSession()),
                        $filtreVille
                    );
                    $this->addFlash('warning', 'Le campus a été supprimé');
                } catch (\Exception $e) {
                    $this->addFlash('danger', $e->getMessage() . ' La suppression du campus a été annulé.');
                }
            } catch (\Exception $e) {
                $this->addFlash('danger', $e->getMessage() . ' Le campus n\'existe pas.');
            } finally {
                return $this->redirectToRoute('app_campus_admin');
            }
        }

        /*************************** Partie Filtre **************************/
        if ($request->request->has('campus_filter') || $request->request->has('campus_reinit')) {
            $message = $filters->filterPage(
                $request,
                'campus_filter',
                'campus_reinit',
                'campus_filter_name',
            );

            $this->addFlash('secondary', $message);
            return $this->redirectToRoute('app_campus_admin');
        }


        /*************************** Standard de la page **************************/
        return $this->render('admin/campus/index.html.twig', [
            'lesCampus' => $lstCampus,
            'formCreate' => $formCreate,
            'filtreNom' => $nameFiltered,
        ]);
    }
}
