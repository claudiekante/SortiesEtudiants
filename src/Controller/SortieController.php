<?php

namespace App\Controller;

use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\LieuType;
use App\Form\RegistrationFormType;
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
    public function creerSortie(Request $request,EtatRepository $etatRepository, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $entityManager): Response
    {
        $utilisateurCourant = $utilisateurRepository->find($this->getUser());

        $sortie = new Sortie();
        $sortieForm = $this->createForm(SortieCreateType::class, $sortie);
        $sortieForm->handleRequest($request);

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $sortie->addParticipant($utilisateurCourant);
            $sortie->setCampus($utilisateurCourant->getCampus());
            $sortie->setOrganisateur($utilisateurCourant);
            $entityManager->persist($sortie);
            $entityManager->flush();

            $this->addFlash('success', 'La sortie a bien été enregistrée');
            return $this->redirectToRoute('main_accueil');
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

            $this->addFlash('success', 'Le lieu a bien été enregistré');
            return $this->redirectToRoute('creer');
        }
        return $this->render('sortie/lieuxSortie.html.twig', [
            'lieuType' => $lieuForm->createView(),


        ]);


    }

    /**
     * @Route ("detailssortie/{id}", name="sortie_detailssortie", methods={"GET"}, requirements={"id"="\d+"})
     */

    public function detailsSortie(SortieRepository $sortieRepository, int $id): Response {
        $sortie = $sortieRepository->find($id);

        return $this->render('sortie/detailssortie.html.twig', [
           'sortie' => $sortie,
        ]);
    }

    /**
     * @Route("/modifier/{id}"), name="modifierSortie"
     */
    public function modifierSortie(int $id, Request $request, SortieRepository $sortieRepository, EntityManagerInterface $em): Response
    {
        $sortie = $sortieRepository->find($id);
        $form = $this->createForm(SortieCreateType::class, $sortie);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $sortie = $form->getData();
            $em->persist($sortie);
            $em->flush();

            $this->addFlash(
                'success',
                'Les modifications ont bien été enregistrées.'
            );
            return $this->redirectToRoute('main_accueil');
        }
        return $this->render('sortie/modifierSortie.html.twig',[
            'form'=>$form->createView(),
            'sortie' =>$sortie,
        ]);


        }

}
