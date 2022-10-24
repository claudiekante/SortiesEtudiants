<?php

namespace App\Api;

use App\Repository\CampusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/apicampus", name="apicampus_")
 */
class ApiCampusController extends AbstractController
{

    /**
     * @Route("/liste", name="liste", methods={"GET"})
     */
    public function liste(CampusRepository $campusRepository): JsonResponse {
        $campus = $campusRepository->findAll();

        return $this->json($campus, Response::HTTP_OK);
    }
}