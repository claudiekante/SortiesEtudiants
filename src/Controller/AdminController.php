<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Ville;
use App\Form\CampusType;
use App\Form\VilleType;
use App\Repository\CampusRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="admin_")
 */

class AdminController extends AbstractController
{
    /**
     * @Route("/control", name="control")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    /**
     * @Route("/listeCampus", name="liste_campus", methods={"GET", "POST"})
     */
    public function listeCampus(EntityManagerInterface $entityManager, Request $request, CampusRepository $campusRepository): Response {

        $listeCampus = $campusRepository->findAll();

        $campus = new Campus();
        $nouveauCampusForm = $this->createForm(CampusType::class, $campus);
        $nouveauCampusForm->handleRequest($request);

        if($nouveauCampusForm->isSubmitted() && $nouveauCampusForm->isValid()) {
            $entityManager->persist($campus);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Le campus a bien été créé'
            );

            return $this->redirectToRoute('admin_liste_campus');
        }

        return $this->render('admin/campus.html.twig', [
            'listeCampus' => $listeCampus,
            'nouveauCampusForm' => $nouveauCampusForm->createView()
        ]);
    }

    /**
     * @Route("/deletecampus/{id}", name="supprimerCampus", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function supprimerCampus(int $id, CampusRepository $campusRepository, EntityManagerInterface $entityManager): Response {

        $campus = $campusRepository->find($id);
        $entityManager->remove($campus);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Le Campus a bien été supprimé'
        );
        return $this->redirectToRoute('admin_liste_campus');
    }

    /**
     * @Route("/modifycampus/{id}", name="modifierCampus", methods={"GET", "POST"}, requirements={"id"="\d+"})
     */
    public function modifierCampus(int $id, Request $request, CampusRepository $campusRepository, EntityManagerInterface $entityManager): Response {

        $campus = $campusRepository->find($id);
        $modifierCampusForm = $this->createForm(CampusType::class, $campus);
        $modifierCampusForm->handleRequest($request);

        if($modifierCampusForm->isSubmitted() && $modifierCampusForm->isValid()) {
            $entityManager->persist($campus);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Le Campus a bien été modifié'
            );

            return $this->redirectToRoute('admin_liste_campus');
        }

        return $this->renderForm('admin/modifiercampus.html.twig', [
            'modifierCampusForm' => $modifierCampusForm
        ]);

    }

    /**
     * @Route("/listevilles", name="liste_villes", methods={"GET", "POST"})
     */
    public function listeVilles(EntityManagerInterface $entityManager, Request $request, VilleRepository $villeRepository): Response {

        $listeVilles = $villeRepository->findAll();

        $ville = new Ville();
        $nouvelleVilleForm = $this->createForm(VilleType::class, $ville);
        $nouvelleVilleForm->handleRequest($request);

        if($nouvelleVilleForm->isSubmitted() && $nouvelleVilleForm->isValid()) {
            $entityManager->persist($ville);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'La ville a bien été créée'
            );

            return $this->redirectToRoute('admin_liste_villes');
        }

        return $this->render('admin/villes.html.twig', [
            'listeVilles' => $listeVilles,
            'nouvelleVilleForm' => $nouvelleVilleForm->createView()
        ]);
    }

    /**
     * @Route("/deleteville/{id}", name="deleteVille", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function supprimerVille(int $id, VilleRepository $villeRepository, EntityManagerInterface $entityManager): Response {

        $ville = $villeRepository->find($id);
        $entityManager->remove($ville);
        $entityManager->flush();

        $this->addFlash(
            'success',
            'La ville a bien été supprimée'
        );
        return $this->redirectToRoute('admin_liste_villes');
    }

    /**
     * @Route("/modifyville/{id}", name="modifierVille", methods={"GET", "POST"}, requirements={"id"="\d+"})
     */
    public function modifierVille(int $id, Request $request, VilleRepository $villeRepository, EntityManagerInterface $entityManager): Response {

        $ville = $villeRepository->find($id);
        $modifierVilleForm = $this->createForm(VilleType::class, $ville);
        $modifierVilleForm->handleRequest($request);

        if($modifierVilleForm->isSubmitted() && $modifierVilleForm->isValid()) {
            $entityManager->persist($ville);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'La ville a bien été modifiée'
            );

            return $this->redirectToRoute('admin_liste_villes');
        }

        return $this->renderForm('admin/modifierville.html.twig', [
            'modifierVilleForm' => $modifierVilleForm
        ]);

    }

    /**
     * @Route("/listeutilisateurs", name="liste_utilisateurs", methods={"GET"})
     */
    public function listeUtilisateurs(EntityManagerInterface $entityManager, Request $request, UtilisateurRepository $utilisateurRepository): Response {

        $listeUtilisateurs = $utilisateurRepository->findAll();

        return $this->render('admin/utilisateurs.html.twig', [
            'listeUtilisateurs' => $listeUtilisateurs
        ]);
    }

}
