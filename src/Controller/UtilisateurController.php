<?php

namespace App\Controller;
use App\Form\ProfilType;
use App\Repository\UtilisateurRepository;
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

class UtilisateurController extends AbstractController
{
    /**
     * @Route("/profil", name="utilisateur_profil", methods={"GET", "POST"})
     */
    public function profil(Request $request, UtilisateurRepository $utilisateurRepository, EntityManagerInterface $entityManager, UserPasswordHasherInterface $userPasswordHasher): Response {
        $utilisateurCourant = $utilisateurRepository->find($this->getUser());
        $form = $this->createForm(ProfilType::class, $utilisateurCourant);
        $form->handleRequest($request);


        if($form->isSubmitted() && $form->isValid()) {

            //          ligne pour récupérer les données de l'image
            $file=$form->get('avatar')->getData();


            //ligne pour récupérer les données de l'image
            $file=$form->get('avatar')->getData();


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