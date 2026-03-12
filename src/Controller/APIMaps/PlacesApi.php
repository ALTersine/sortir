<?php

namespace App\Controller\APIMaps;


use App\Repository\VilleRepository;
use App\Service\GooglePlacesService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class PlacesApi extends AbstractController
{
    #[Route('/api/places', methods: ['POST'])]
    public function search(
        Request             $request,
        GooglePlacesService $api,
        VilleRepository $repo
    ): JsonResponse
    {
        $data = $request->toArray();

        //todo: remettre bien une fois qu'on passe par l'interface
        /*$recherche = $data["recherche"] ?? null;
        $ville = $data["ville"] ?? null;*/
        $recherche = $data["recherche"] ?? null;
        $ville = $repo->findOneBy(["name" => $data["ville"]]);


        if (!$recherche) {
            return $this->json([
                'error' => 'Il vous manque une indication de lieu à rechercher'
            ], 400);
        }
        if (!$ville) {
            return $this->json([
                'error' => 'Erreur dans l\'attribution d\'une ville pour la recherche'
            ], 400);
        }

        $resultat = $api->searchPlaces($recherche,$ville);
        return $this->json($resultat);
    }

}
