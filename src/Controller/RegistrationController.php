<?php

namespace App\Controller;

use App\Entity\Campus;
use App\Entity\Utilisateur;
use App\Form\RegistrationFormType;
use App\Security\AppAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, AppAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new Utilisateur();
        $user->setAdministrateur(false);
        $user->setActif(true);
        $user->setRoles(["ROLE_USER"]);

        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {

          //          ligne pour récupérer les données de l'image
          $file=$form->get('avatar')->getData();

//             encode the plain password
            $user->setPassword(
            $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->persist($user);
            $entityManager->flush();
            // do anything else you need here, like send an email

          //ligne pour transferer l'image et la renommer en bdd
          if ($file){
              $newFilename = $user->getPseudo()."-".$user->getId().".".$file->guessExtension();
              $file->move($this->getParameter('upload_champ_entite_dir'), $newFilename);
              $user->setAvatar($newFilename);
          }

          //il faut repeter le flush
          $entityManager->persist($user);
          $entityManager->flush();

            return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }
}
