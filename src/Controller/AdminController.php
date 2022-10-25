<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Repository\CampusRepository;
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


}
