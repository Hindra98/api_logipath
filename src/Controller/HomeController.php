<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home', methods: ['GET'])]
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
