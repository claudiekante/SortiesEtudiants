<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Form\CampusType;
use App\Repository\CampusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
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
     * @Route("/deletecampus/{id}"), name="supprimerCampus", methods={"GET"}, requirements={"id"="\d+"})
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
     * @Route
     */
    public function ajoutUtilisateurByfichierCsv (KernelInterface $kernel):Response
    {
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput([
            'command' => 'app:create-users-from-file',
            // (optional) define the value of command arguments
            'fooArgument' => 'barValue',
            // (optional) pass options to the command
            '--bar' => 'fooValue',
        ]);

        // You can use NullOutput() if you don't need the output
        $output = new BufferedOutput();
        $application->run($input, $output);

        // return the output, don't use if you used NullOutput()
        $content = $output->fetch();

        // return new Response(""), if you used NullOutput()
        return new Response($content);
    }

}
