<?php

namespace App\Controller;
use App\Form\RegistrationFormType;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UtilisateurController extends AbstractController
{
    /**
     * @Route("/profil", name="utilisateur_profil", methods={"GET", "POST"})
     */
    public function profil(Request $request, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response {
        $utilisateurCourant = $utilisateurRepository->find($this->getUser());
        $form = $this->createForm(RegistrationFormType::class, $utilisateurCourant);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {

            //          ligne pour récupérer les données de l'image
            $file=$form->get('avatar')->getData();


            //ligne pour récupérer les données de l'image
            $file=$form->get('avatar')->getData();

            $utilisateurCourant->setPassword(
                $userPasswordHasher->hashPassword(
                    $utilisateurCourant,
                    $form->get('plainPassword')->getData()
                )
            );

            //          ligne pour récupérer les données de l'image
            if ($file){
 //               $file->move($this->getParameter('upload_champ_entite_dir'));

                $newFilename = $utilisateurCourant->getPseudo()."-".$utilisateurCourant->getId().".".$file->guessExtension();
                $file->move($this->getParameter('upload_champ_entite_dir'), $newFilename);
                $utilisateurCourant->setAvatar($newFilename);
            }

            $entityManager->persist($utilisateurCourant);
            $entityManager->flush();

            $this->addFlash('success', 'La modification a bien été enregistrée');
            return $this->redirectToRoute("main_accueil");
        }

        return $this->renderForm('utilisateur/profil.html.twig', [
            'form' => $form
        ]);
    }

    /**
     * @Route ("/profil/{id}" ,name="utilisateur_profilid", methods={"GET"}, requirements={"id"="\d+"})
     */

    public function profilid(UtilisateurRepository $utilisateurRepository, int $id): Response {
        $profil = $utilisateurRepository->find($id);

        return $this->render('utilisateur/profilid.html.twig', [
            'profil' => $profil,
        ]);
    }
}