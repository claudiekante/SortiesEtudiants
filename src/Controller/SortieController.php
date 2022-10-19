<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\LieuType;
use App\Form\SortieCreateType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class SortieController extends AbstractController
{
    /**
     * @Route("/sortie", name="app_sortie")
     */
    public function index(): Response
    {
        return $this->render('sortie/index.html.twig', [
            'controller_name' => 'SortieController',
        ]);
    }
    /**
     * @Route("/creer", name="creer")
     */
    public function creerSortie(Request $request, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $entityManager): Response
    {
        $utilisateurCourant = $utilisateurRepository->find($this->getUser());

        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieCreateType::class, $sortie);
        $sortie->setCampus($utilisateurCourant->getCampus());
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $entityManager->persist($sortie);
            $entityManager->flush();
        }

        return $this->render('sortie/createSortie.html.twig', [
            'sortieCreateType' => $sortieForm->createView(),
            'utilisateurCourant'=>$utilisateurCourant
        ]);
    }

    /**
     * @Route("/lieu", name="/lieu")
     */
    public function choixLieu(Request $request, EntityManagerInterface $entityManager): Response
    {
        $lieu = new Lieu();
        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $lieuForm->handleRequest($request);
        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {
            $entityManager->persist($lieu);
            $entityManager->flush();
        }
        return $this->render('sortie/lieuxSortie.html.twig', [
            'lieuType' => $lieuForm->createView(),

        ]);
    }

}
