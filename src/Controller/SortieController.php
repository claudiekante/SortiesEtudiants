<?php

namespace App\Controller;

use App\Entity\Sortie;
use App\Form\SortieCreateType;
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
    public function creerSortie(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sortie = new Sortie();

        $sortieForm = $this->createForm(SortieCreateType::class, $sortie);
        $sortieForm-> handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()){

            $entityManager->persist($sortie);
            $entityManager->flush();

        }
        return $this->render('sortie/createSortie.html.twig', [
            'sortieCreateType' => $sortieForm->createView(),
        ]);
    }
}
