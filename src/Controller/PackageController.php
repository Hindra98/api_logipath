<?php

namespace App\Controller;

use App\Repository\PackageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PackageController extends AbstractController
{
    #[Route('/api', name: 'api_')]
    public function index(): Response
    {
        return $this->render('package/index.html.twig', [
            'controller_name' => 'PackageController',
        ]);
    }
    /**
     * Endpoint pour que Make.com / Evolution API vérifie le statut d'un colis
     * Route: GET /api/package/LP-12345
     */
    #[Route('/package/{trackingNumber}', name: 'app_package_status', methods: ['GET'])]
    public function getPackageStatus(string $trackingNumber, PackageRepository $packageRepository): Response
    {        // Ici, vous devriez normalement interroger votre base de données pour obtenir le statut du colis

    $package = $packageRepository->findOneBy(['trackingNumber' => $trackingNumber]);

    if(!$package) {
        return $this->json([
          'success' => false,
          'message' => 'Colis introuvale. Verifiez le numéro de suivi et réessayez.',
          'tracking_number' => $trackingNumber,
            'error' => 'Package not found'
        ], Response::HTTP_NOT_FOUND);
    }


        // 3. Renvoi des données formatées pour Gemini/Make
        return $this->json([
            'success' => true,
            'data' => [
                'id' => $package->getId(),
                'tracking_number' => $package->getTrackingNumber(),
                'client' => $package->getCustomerName(),
                'statut_actuel' => $package->getStatus(), // ex: "Arrivé au port de Douala"
                'destination' => $package->getDestination(),
                'date_estimee' => $package->getEstimatedDelivery()?->format('d-m-Y'),
                'derniere_mise_a_jour' => $package->getUpdatedAt()?->format('d-m-Y H:i')
            ]
        ]);
    }
}
