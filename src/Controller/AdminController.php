<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Utilisateur;
use App\Entity\Ville;
use App\Form\CampusType;
use App\Form\RegistrationFormType;
use App\Form\VilleType;
use App\Repository\CampusRepository;
use App\Repository\UtilisateurRepository;
use App\Repository\VilleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
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
        public
        function modifierCampus(int $id, Request $request, CampusRepository $campusRepository, EntityManagerInterface $entityManager): Response
        {

            $campus = $campusRepository->find($id);
            $modifierCampusForm = $this->createForm(CampusType::class, $campus);
            $modifierCampusForm->handleRequest($request);

            if ($modifierCampusForm->isSubmitted() && $modifierCampusForm->isValid()) {
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
        public
        function listeVilles(EntityManagerInterface $entityManager, Request $request, VilleRepository $villeRepository): Response
        {

            $listeVilles = $villeRepository->findAll();

            $ville = new Ville();
            $nouvelleVilleForm = $this->createForm(VilleType::class, $ville);
            $nouvelleVilleForm->handleRequest($request);

            if ($nouvelleVilleForm->isSubmitted() && $nouvelleVilleForm->isValid()) {
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
        public
        function supprimerVille(int $id, VilleRepository $villeRepository, EntityManagerInterface $entityManager): Response
        {

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
        public
        function modifierVille(int $id, Request $request, VilleRepository $villeRepository, EntityManagerInterface $entityManager): Response
        {

            $ville = $villeRepository->find($id);
            $modifierVilleForm = $this->createForm(VilleType::class, $ville);
            $modifierVilleForm->handleRequest($request);

            if ($modifierVilleForm->isSubmitted() && $modifierVilleForm->isValid()) {
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
        public
        function listeUtilisateurs(EntityManagerInterface $entityManager, Request $request, UtilisateurRepository $utilisateurRepository): Response
        {

            $listeUtilisateurs = $utilisateurRepository->findAllUsers();

            return $this->render('admin/utilisateurs.html.twig', [
                'listeUtilisateurs' => $listeUtilisateurs
            ]);
        }

        /**
         * @Route("/inscrireUtilisateur", name="inscrire_utilisateur", methods={"GET", "POST"})
         */
        public
        function inscrireUtilisateur(UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, Request $request, UtilisateurRepository $utilisateurRepository): Response
        {

            $user = new Utilisateur();
            $user->setAdministrateur(false);
            $user->setActif(true);

            $form = $this->createForm(RegistrationFormType::class, $user);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {

                //          ligne pour récupérer les données de l'image
                $file = $form->get('avatar')->getData();

//             encode the plain password
                $user->setPassword(
                    $userPasswordHasher->hashPassword(
                        $user,
                        $form->get('plainPassword')->getData()
                    )
                );

                if ($user->isAdministrateur()) {
                    $user->setRoles(["ROLE_ADMIN"]);
                } else {
                    $user->setRoles(["ROLE_USER"]);
                }

                $entityManager->persist($user);
                $entityManager->flush();
                // do anything else you need here, like send an email

                //ligne pour transferer l'image et la renommer en bdd
                if ($file) {
                    $newFilename = $user->getPseudo() . "-" . $user->getId() . "." . $file->guessExtension();
                    $file->move($this->getParameter('upload_champ_entite_dir'), $newFilename);
                    $user->setAvatar($newFilename);
                }

                //il faut repeter le flush
                $entityManager->persist($user);
                $entityManager->flush();

                $this->addFlash(
                    'success',
                    'L\'utilisateur a été créé avec succès'
                );

                return $this->redirectToRoute('admin_liste_utilisateurs');

            }
            return $this->render('admin/inscrireutilisateur.html.twig', [
                'form' => $form->createView()
            ]);
        }

        /**
         * @Route("/toggleUtilisateur/{id}", name="toggleUtilisateur", methods={"GET"}, requirements={"id"="\d+"})
         */
        public
        function toggleUtilisateur(int $id, EntityManagerInterface $entityManager, Request $request, UtilisateurRepository $utilisateurRepository): Response
        {

            $utilisateur = $utilisateurRepository->find($id);

            if ($utilisateur->isActif()) {
                $utilisateur->setActif(false);
                $utilisateur->setRoles(['INACTIF']);
            } else {
                $utilisateur->setActif(true);
                $utilisateur->setRoles(['ROLE_USER']);
            }


            $entityManager->persist($utilisateur);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Utilisateur modifié avec succès'
            );

            return $this->redirectToRoute('admin_liste_utilisateurs');
        }

        /**
         * @Route("/deleteutilisateur/{id}", name="deleteUtilisateur", methods={"GET"}, requirements={"id"="\d+"})
         */
        public
        function supprimerUtilisateur(int $id, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $entityManager): Response
        {

            $utilisater = $utilisateurRepository->find($id);
            $entityManager->remove($utilisater);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'L\'utilisateur a bien été supprimé'
            );
            return $this->redirectToRoute('admin_liste_utilisateurs');
        }

    /**
     * @Route("/import-csv", name="app_import_csv", methods={"GET"})
     */
    public function importCsv(EntityManagerInterface $em): Response
    {
        // Pour la phase de développement, on peut vider la table...
        // ... à chaque fois avec l'instruction SQL 'TRUNCATE'
        // cf. https://code2dev.go.yo.fr/cours/symfony/doctrine_faq.php#h2_6
        $connection = $em->getConnection();
        $platform = $connection->getDatabasePlatform();
       // $connection->executeQuery($platform->getTruncateTableSQL('utilisateur'));

        // Chemin vers le fichier
        // $this->getParameter('kernel.project_dir') récupère le chemin racine du projet (genre 'c:/wamp/www/projet')
        $sFile = $this->getParameter('kernel.project_dir').'/public/data/listeUsers.csv';

        // On déclare un tableau qui stockera les nouveaux participants pour les afficher
        $aNewParticipants = [];

        // On ouvre le fichier en mode lecture ('r')
        // $handle représente le fichier = une ressource (une sorte d'objet avec les métadonnées du fichier)
        // On teste que cette ressource est bien présente (!== false)
        if (($handle = fopen($sFile, "r")) !== FALSE)
        {
            // On parcourt le fichier ligne par ligne
            // fgetcsv() découpe chaque ligne par rapport au séparateur (';')
            // et met retourne les données dans un tableau, ici $aLine
            // les indices du tableau coorespondent à la position des valeurs/colonnes dans le fichier CSV
            while ( ($aLine = fgetcsv($handle, 1024, ";") ) !== FALSE)
            {
                // Pour chaque nouvelle ligne, on crée un objet Participant
                $participant = new Utilisateur();

                // On assigne telle colonne comme valeur de telle propriété (vérifier quelle valeur correspond à quelle colonne) :
                $participant->setEmail($aLine[0]);

                // Dans le fichier les rôles ont bien des crochets
                // mais la lecture du fichier les retourne comme chaînes
                // la fonction json_decode() nous aide à obtenir un tableau
                 //$aRoles = json_decode($aLine[1], TRUE);
               // $participant->setRoles($aRoles);
                $participant->setRoles(["ROLE_USER"]);
                $participant->setPassword($aLine[1]);
                $participant->setNom($aLine[2]);
                $participant->setPrenom($aLine[3]);
                $participant->setActif($aLine[4]);
                $participant->setAdministrateur(true);
                $participant->setPseudo($aLine[5]);
                $participant->setTelephone($aLine[6]);
                // etc. pour chaque colonne/setter (à adapter à votre entité/fichier/données attendues)

                // On persiste l'objet courant
                $em->persist($participant);

                // Comme on souhaite afficher les nouveaux participants dans la vue,
                // on est obligé de faire le flush ici, ainsi on peut récupérer l'id créé
                $em->flush();

                // On met le nouveau participant dans le tableau à transmettre à la vue
                $aNewParticipants[] = $participant;

            }

            // On ferme le fichier (= supprime la ressource en mémoire)  s
            fclose($handle);

            $this->addFlash(
                'success',
                'Utilisateur modifié avec succès');
        }

        // On appelle la vue en lui passant le tableau des nouveaux participants
        return $this->render('admin/utilisateurs.html.twig', ['aNewParticipants' => $aNewParticipants]);
    }





}
