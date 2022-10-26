<?php

namespace App\Controller;

//use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Form\LieuType;
use App\Form\MotifAnnulationType;
//use App\Form\RegistrationFormType;
use App\Form\SortieCreateType;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use App\Repository\UtilisateurRepository;
//use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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
            'controller_name' => 'SortieController'
        ]);
    }

    /**
     * @Route("/creer", name="creer")
     * @param Request $request
     * @param EtatRepository $etatRepository
     * @param UtilisateurRepository $utilisateurRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function creerSortie(Request $request, EtatRepository $etatRepository, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $entityManager): Response
    {
        $choixUtilisateur = $request->request->get('createSortie');
        if ($choixUtilisateur == 'Ouverte') {
            $etatCreee = $etatRepository->findByLibelle('Ouverte');
        }
        if ($choixUtilisateur == 'Créée') {
            $etatCreee = $etatRepository->findByLibelle('Créée');
        }

        $sortie = new Sortie();
        $utilisateurCourant = $utilisateurRepository->find($this->getUser());
        $sortieForm = $this->createForm(SortieCreateType::class, $sortie);

        $sortieForm->handleRequest($request);

        $lieu = new Lieu();
        $lieuForm = $this->createForm(LieuType::class, $lieu);
        $lieuForm->handleRequest($request);

        if ($lieuForm->isSubmitted() && $lieuForm->isValid()) {
            $entityManager->persist($lieu);
            $entityManager->flush();

            $this->addFlash('success', 'Le lieu a bien été enregistré');
            return $this->redirectToRoute('creer');
        }

        if ($sortieForm->isSubmitted() && $sortieForm->isValid()) {
            $sortie->setEtat($etatCreee);
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
            'utilisateurCourant' => $utilisateurCourant,
            'lieuType' => $lieuForm->createView(),
            'sortie' => $sortie,

        ]);
    }

    /**
     * @Route ("detailssortie/{id}", name="sortie_detailssortie", requirements={"id"="\d+"})
     */

    public function detailsSortie(SortieRepository $sortieRepository, Request $request,EtatRepository $etatRepository,int $id, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $em): Response
    {
        $utilisateurCourant = $utilisateurRepository->find($this->getUser());
        $sortie = $sortieRepository->findOneSortie($id);
        $etatCreee = $etatRepository->findByLibelle('Annulée');
        $motifAnnulationForm = $this->createForm(MotifAnnulationType::class, $sortie);
        $motifAnnulationForm->handleRequest($request);

        if ($motifAnnulationForm->isSubmitted() && $motifAnnulationForm->isValid()) {
            $sortie->setEtat($etatCreee);
            $em->persist($sortie);
            $em->flush();
            $this->addFlash(
                'success',
                'La sortie a bien été annulée'
            );
            return $this->redirectToRoute('main_accueil');
        }
        return $this->render('sortie/detailssortie.html.twig', [

            'sortie' => $sortie,
            'utilisateurCourant' => $utilisateurCourant,
            'motifAnnulationForm' => $motifAnnulationForm->createView(),
        ]);
    }

    /**
     * @Route("/modifier/{id}"), name="modifierSortie"
     */
    public function modifierSortie(int $id, EtatRepository $etatRepository, Request $request, SortieRepository $sortieRepository, EntityManagerInterface $em): Response
    {
        $choixUtilisateur = $request->request->get('createSortie');
        if ($choixUtilisateur == 'Ouverte') {
            $etatCreee = $etatRepository->findByLibelle('Ouverte');
        }
        if ($choixUtilisateur == 'Créée') {
            $etatCreee = $etatRepository->findByLibelle('Créée');
        }

        $sortie = $sortieRepository->find($id);
        $form = $this->createForm(SortieCreateType::class, $sortie);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $sortie = $form->getData();
            $sortie->setEtat($etatCreee);
            $em->persist($sortie);
            $em->flush();

            $this->addFlash(
                'success',
                'Les modifications ont bien été enregistrées.'
            );
            return $this->redirectToRoute('main_accueil');
        }
        return $this->render('sortie/modifierSortie.html.twig', [
            'form' => $form->createView(),
            'sortie' => $sortie,

        ]);
    }

    /**
     * @Route("/participer/{id}"), name"participerSortie", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function participerSortie(int $id, Request $request, SortieRepository $sortieRepository, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $em): Response
    {
        $sortie = $sortieRepository->find($id);
        $motifAnnulationForm = $this->createForm(MotifAnnulationType::class, $sortie);
        $utilisateurCourant = $utilisateurRepository->find($this->getUser());
        $sortie->addParticipant($utilisateurCourant);
        $em->persist($sortie);
        $em->flush();
        $this->addFlash(
            'success',
            'Tu es maintenant inscrit.'

        );

        $em->refresh($sortie);
        return $this->render('sortie/detailssortie.html.twig', [
            'sortie' => $sortie,
            'motifAnnulationForm' => $motifAnnulationForm->createView(),
        ]);

    }

    /**
     * @Route("/nonparticiper/{id}"), name"nonparticiperSortie", methods={"GET"}, requirements={"id"="\d+"})
     */
    public function nonparticiperSortie(int $id, Request $request, SortieRepository $sortieRepository, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $em): Response
    {
        $sortie = $sortieRepository->find($id);
        $motifAnnulationForm = $this->createForm(MotifAnnulationType::class, $sortie);
        $utilisateurCourant = $utilisateurRepository->find($this->getUser());
        $sortie->removeParticipant($utilisateurCourant);
        $em->persist($sortie);
        $em->flush();
        $this->addFlash(
            'success',
            'Tu es maintenant désinscrit.'
        );

        $em->refresh($sortie);
        return $this->render('sortie/detailssortie.html.twig', [
            'sortie' => $sortie,
            'motifAnnulationForm' => $motifAnnulationForm->createView(),
        ]);

    }


//        if($form->isSubmitted() && $form->isValid()) {
//            $sortie = $form->getData();
//            $em->persist($sortie);
//            $em->flush();
//
//            $this->addFlash(
//                'success',
//                'Les modifications ont bien été enregistrées.'
//            );
//            return $this->redirectToRoute('main_accueil');
//        }
//        return $this->render('sortie/modifierSortie.html.twig',[
//            'form'=>$form->createView(),
//            'sortie' =>$sortie,
//        ]);
//
//
//    }


}
