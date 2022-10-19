<?php

namespace App\Controller;

use App\Form\ListeSortiesType;
use App\Repository\SortieRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_accueil", methods={"GET"})
     */

    public function accueil(Request $request, SortieRepository $sortieRepository): Response {

        $sorties = $sortieRepository->listSortie();

        $searchForm = $this->createForm(ListeSortiesType::class);
        $searchForm->handleRequest($request);

        return $this->render('main/accueil.html.twig', [
            "sorties" => $sorties,
            "searchForm" => $searchForm->createView(),
        ]);
    }
}