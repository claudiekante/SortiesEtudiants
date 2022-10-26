<?php

namespace App\Controller;

use App\Form\ListeSortieType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UtilisateurRepository;
use App\Services\EtatService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main_accueil", methods={"GET", "POST"})
     */

    public function accueil(Request $request, SortieRepository $sortieRepository, UtilisateurRepository $utilisateurRepository, EtatRepository $etatRepository, EtatService $etatService, EntityManagerInterface $entityManager): Response {


        $etatService->updateEtatSortie($sortieRepository, $etatRepository, $entityManager);

        $sorties = $sortieRepository->listSortie();

        $searchForm = $this->createForm(ListeSortieType::class);
        $searchForm->handleRequest($request);

        if($searchForm->isSubmitted() && $searchForm->isValid()) {
            $utilisateurCourant = $utilisateurRepository->find($this->getUser());

            $mots = $searchForm->get('search')->getData();
            $campus = $searchForm->get('campus')->getData();
            $ouvertes = $searchForm->get('ouvertes')->getData();
            $organisateur = $searchForm->get('organisateur')->getData();
            $inscrit = $searchForm->get('inscrit')->getData();
            $pasInscrit = $searchForm->get('pasInscrit')->getData();
            $dejaPassee = $searchForm->get('dejaPassee')->getData();
            $dateDebut = $searchForm->get('dateDebut')->getData();
            $dateFin = $searchForm->get('dateFin')->getData();

            $sorties = $sortieRepository->search($utilisateurCourant->getId(), $mots, $campus, $ouvertes, $organisateur,  $inscrit, $pasInscrit, $dejaPassee, $dateDebut, $dateFin);


            return $this->render('main/accueil.html.twig', [
                "sorties" => $sorties,
                "searchForm" => $searchForm->createView(),
            ]);
        }

        return $this->render('main/accueil.html.twig', [
            "sorties" => $sorties,
            "searchForm" => $searchForm->createView(),
        ]);
    }
}