<?php

namespace App\Controller;

use OpenApi\Attributes as OA;
// use Nelmio\ApiDocBundle\Annotation\Model;
use App\Entity\Package;
use App\Repository\PackageRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Nelmio\ApiDocBundle\Attribute\Model;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/package')]
#[OA\Tag(name: 'Packages')]
class PackageController extends AbstractController
{
  /**
   * Endpoint pour que Make.com / Evolution API vérifie le statut d'un colis
   * Route: GET /package/LP-12345
   */
  #[Route('/{trackingNumber}', name: 'app_package_status', methods: ['GET'])]
  #[OA\Parameter(
    name: 'trackingNumber',
    in: 'path',
    description: 'Numéro de suivi du colis',
    schema: new OA\Schema(type: 'string')
  )]
  #[OA\Response(
    response: 200,
    description: 'Retourne les détails d\'un colis',
    content: new OA\JsonContent(
      type: 'object',
      properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true, description: 'Indique si l\'opération a réussi'),
        new OA\Property(property: 'message', type: 'string', example: '', description: 'Message de retour'),
        new OA\Property(
          property: 'data',
          type: 'object',
          ref: new Model(type: Package::class),
          description: 'Données formatées concernant le colis'
        )
      ]
    )
  )]
  #[OA\Response(
    response: 404,
    description: 'Colis non trouvé',
    content: new OA\JsonContent(
      type: 'object',
      properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false, description: 'Indique si l\'opération a réussi'),
        new OA\Property(property: 'message', type: 'string', example: 'Colis introuvale. Verifiez le numéro de suivi et réessayez.', description: 'Message de retour'),
        new OA\Property(property: 'error', type: 'string', example: 'Aucun colis trouvé', description: 'Package not found'),
      ]
    )
  )]
  #[OA\Response(
    response: 500,
    description: 'Erreur serveur',
    content: new OA\JsonContent(
      type: 'object',
      properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false, description: 'Indique si l\'opération a réussi'),
        new OA\Property(property: 'message', type: 'string', example: 'Erreur interne du serveur.', description: 'Message de retour'),
        new OA\Property(property: 'error', type: 'string', example: 'Erreur interne du serveur', description: 'Message d\'erreur détaillé'),
      ]
    )
  )]
  public function getPackage(string $trackingNumber, PackageRepository $packageRepository): Response
  {

    $package = $packageRepository->findOneBy(['trackingNumber' => $trackingNumber]);

    if (!$package) {
      return $this->json([
        'success' => false,
        'data' => null,
        'message' => 'Colis introuvale. Verifiez le numéro de suivi et réessayez.',
        'tracking_number' => $trackingNumber,
        'error' => 'Package not found'
      ], Response::HTTP_NOT_FOUND);
    }


    // 3. Renvoi des données formatées pour Gemini/Make
    return $this->json([
      'success' => true,
      'message' => '',
      'data' => [
        'id' => $package->getId(),
        'tracking_number' => $package->getTrackingNumber(),
        'client' => $package->getCustomerName(),
        'statut_actuel' => $package->getStatus(), // ex: "Arrivé au port de Douala"
        'destination' => $package->getDestination(),
        'date_estimee' => $package->getEstimatedDelivery()?->format('d-m-Y'),
        'derniere_mise_a_jour' => $package->getUpdatedAt()?->format('d-m-Y à H:i')
      ]
    ]);
  }

  /**
   * Endpoint pour que Make.com / Evolution API affiche tous les colis
   * Route: GET /package
   */
  #[Route('', name: 'app_packages', methods: ['GET'])]
  #[OA\Response(
    response: 200,
    description: 'Endpoint pour que Make.com / Evolution API affiche tous les colis',
    content: new OA\JsonContent(
      type: 'object',
      properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true, description: 'Indique si l\'opération a réussi'),
        new OA\Property(property: 'message', type: 'string', example: '', description: 'Message de retour'),
        new OA\Property(
          property: 'data',
          type: 'object',
          description: 'Données formatées concernant les colis',
          properties: [
            // new OA\Property(property: 'success', type: 'boolean', example: true, description: 'Indique si l\'opération a réussi'),
            // new OA\Property(property: 'message', type: 'string', example: '', description: 'Message de retour'),
            new OA\Property(
              property: 'packages',
              type: 'array',
              items: new OA\Items(ref: new Model(type: Package::class)),
              description: 'Liste des colis'
            )
          ]
        )
      ]
    )
  )]
  #[OA\Response(
    response: 404,
    description: 'Aucun colis enregistré',
    content: new OA\JsonContent(
      type: 'object',
      properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false, description: 'Indique si l\'opération a réussi'),
        new OA\Property(property: 'message', type: 'string', example: 'Liste des colis vide.', description: 'Message de retour'),
        new OA\Property(property: 'error', type: 'string', example: 'Aucun colis trouvé', description: 'Message d\'erreur détaillé'),
      ]
    )
  )]
  #[OA\Response(
    response: 500,
    description: 'Erreur serveur',
    content: new OA\JsonContent(
      type: 'object',
      properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false, description: 'Indique si l\'opération a réussi'),
        new OA\Property(property: 'message', type: 'string', example: 'Erreur interne du serveur.', description: 'Message de retour'),
        new OA\Property(property: 'error', type: 'string', example: 'Erreur interne du serveur', description: 'Message d\'erreur détaillé'),
      ]
    )
  )]
  public function getPackages(PackageRepository $packageRepository): Response
  {

    $packages = $packageRepository->findAll();

    if (!$packages || count($packages) === 0) {
      return $this->json([
        'success' => false,
        'data' => null,
        'message' => 'Liste des colis vide',
        'error' => 'Aucun colis trouvé'
      ], Response::HTTP_NOT_FOUND);
    }

    // 3. Renvoi des données formatées pour Gemini/Make
    return $this->json([
      'success' => true,
      'message' => '',
      'data' => [
        'packages' => $packages,
      ]
    ]);
  }

  /**
   * Endpoint pour créer ou mettre à jour un colis
   * Route: POST /api/package
   */
  #[Route('', name: 'app_package_save', methods: ['POST'])]
  #[OA\Post(
    path: '/api/package',
    summary: 'Créer ou mettre à jour un colis',
    description: 'Si le trackingNumber existe déjà, le colis est mis à jour.'
  )]
  #[OA\RequestBody(
    required: true,
    content: new OA\JsonContent(
      properties: [
        new OA\Property(property: 'trackingNumber', type: 'string', example: 'LGP-123', description: 'Numéro de suivi du colis'),
        new OA\Property(property: 'status', type: 'string', example: 'Expédié', description: 'Statut actuel du colis'),
        new OA\Property(property: 'customerName', type: 'string', example: 'John Doe', description: 'Nom du client'),
        new OA\Property(property: 'destination', type: 'string', example: 'Douala', description: 'Destination du colis'),
        new OA\Property(property: 'estimatedDelivery', type: 'string', example: '2023-12-31', description: 'Date de livraison estimée au format YYYY-MM-DD')
      ]
    )
  )]
  #[OA\Response(
    response: 200,
    description: 'Colis enregistré ou mis à jour avec succès',
    content: new OA\JsonContent(
      type: 'object',
      properties: [
        new OA\Property(property: 'success', type: 'boolean', example: true, description: 'Indique si l\'opération a réussi'),
        new OA\Property(property: 'message', type: 'string', example: 'Colis créé avec succès.', description: 'Message de retour'),
        new OA\Property(property: 'data', type: 'object', ref: new Model(type: Package::class), description: 'Colis créé ou mis à jour'),
      ]
    )
  )]
  #[OA\Response(
    response: 400,
    description: 'Erreur de validation des données',
    content: new OA\JsonContent(
      type: 'object',
      properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false, description: 'Indique si l\'opération a réussi'),
        new OA\Property(property: 'message', type: 'string', example: 'Colis créé avec succès.', description: 'Message de retour'),
        new OA\Property(property: 'error', type: 'string', example: 'Donnees manquantes', description: 'Message d\'erreur détaillé'),
      ]
    )
  )]
  #[OA\Response(
    response: 500,
    description: 'Erreur serveur',
    content: new OA\JsonContent(
      type: 'object',
      properties: [
        new OA\Property(property: 'success', type: 'boolean', example: false, description: 'Indique si l\'opération a réussi'),
        new OA\Property(property: 'message', type: 'string', example: 'Erreur interne du serveur.', description: 'Message de retour'),
        new OA\Property(property: 'error', type: 'string', example: 'Erreur interne du serveur', description: 'Message d\'erreur détaillé'),
      ]
    )
  )]
  public function savePackage(
    Request $request,
    PackageRepository $packageRepository,
    EntityManagerInterface $entityManager,
    ValidatorInterface $validator
  ): Response {
    try {
      // Récupération des données JSON
      $data = json_decode($request->getContent(), true);
      $isNew = false;

      // Validation des données requises
      if (!isset($data['trackingNumber'])) {
        if (!isset($data['trackingNumber']))
          return $this->json([
            'success' => false,
            'message' => 'Le numéro de suivi (trackingNumber) est requis.',
            'error' => 'Missing tracking number'
          ], Response::HTTP_BAD_REQUEST);
      }

      $trackingNumber = $data['trackingNumber'];

      // Chercher le colis existant
      $package = $packageRepository->findOneBy(['trackingNumber' => $trackingNumber ?? ""]);

      // Créer un nouveau colis si inexistant
      if (!$package) {
        if (!isset($data['customerName']))
          return $this->json([
            'success' => false,
            'message' => 'Le nom du client (customerName) est requis.',
            'error' => 'Missing customer name'
          ], Response::HTTP_BAD_REQUEST);
        if (!isset($data['destination']))
          return $this->json([
            'success' => false,
            'message' => 'La destination (destination) est requise.',
            'error' => 'Missing destination'
          ], Response::HTTP_BAD_REQUEST);
        $package = new Package();
        $package->setTrackingNumber($trackingNumber);
        $isNew = true;
      }
      // Mise à jour des champs fournis
      $package->setStatus($data['status'] ?? $package->getStatus() ?? "En entrepôt"); // ex: (reçu, en entrepôt, en mer/air, à Douala, livré)
      $package->setCustomerName($data['customerName'] ?? $package->getCustomerName());
      $package->setDestination($data['destination'] ?? $package->getDestination());

      if (isset($data['estimatedDelivery'])) {
        try {
          $package->setEstimatedDelivery(new \DateTime($data['estimatedDelivery']));
        } catch (\Exception $e) {
          return $this->json([
            'success' => false,
            'message' => 'Format de date invalide pour estimatedDelivery. Format attendu: YYYY-MM-DD ou ISO 8601',
            'error' => 'Invalid date format'
          ], Response::HTTP_BAD_REQUEST);
        }
      }

      // Mise à jour de la date de modification
      $package->setUpdatedAt(new \DateTimeImmutable());
      // 4. Validation
      $errors = $validator->validate($package);
      if (count($errors) > 0) {
        return $this->json([
          'success' => false,
          'message' => 'Une erreur est survenue lors du traitement.',
          'error' => $errors->__toString()
        ], Response::HTTP_BAD_REQUEST);
      }

      // Sauvegarde en base de données
      $entityManager->persist($package);
      $entityManager->flush();

      return $this->json([
        'success' => true,
        'message' => $isNew ? 'Colis créé avec succès.' : 'Colis mis à jour avec succès.',
        'data' => $package
      ], $isNew ? Response::HTTP_CREATED : Response::HTTP_OK);
    } catch (\Exception $e) {
      return $this->json([
        'success' => false,
        'message' => 'Une erreur est survenue lors du traitement.',
        'error' => $e->getMessage()
      ], Response::HTTP_INTERNAL_SERVER_ERROR);
    }
  }
}
