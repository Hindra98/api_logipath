<?php

namespace App\Controller;

use OpenApi\Attributes as OA;
use Nelmio\ApiDocBundle\Annotation\Model;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
  #[Route('/api', name: 'app_home', methods: ['GET'])]
  #[OA\Response(
    response: 200,
    description: 'Retourne les détails d\'un colis',
    content: new OA\JsonContent(type: 'object')
  )]
  #[OA\Tag(name: 'Index')]
  public function index(): JsonResponse
  {
    return $this->json([
      'status' => 'online',
      'message' => 'Logipath Backend API is running successfully.',
      'environment' => $this->getParameter('kernel.environment'),
      'timestamp' => time(),
    ]);
  }
}
