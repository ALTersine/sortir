<?php

namespace App\Controller;

use App\Repository\VilleRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class VilleController extends AbstractController
{
    #[Route('/ville/cp', name: 'app_ville_cp')]
    public function getCp(
        Request $request,
        VilleRepository $villeRepository,
    ): JsonResponse
    {
        $villeId = $request->query->get('ville');
        $ville = $villeRepository->findOneBy(['id' => $villeId]);

        return $this->json([
            'codePostal' => $ville?->getCodePostal(),
        ]);
    }
}
