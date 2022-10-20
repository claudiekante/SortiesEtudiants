<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use App\Entity\Lieu;
use App\Entity\Sortie;
use App\Entity\Utilisateur;
use App\Entity\Ville;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $faker = \Faker\Factory::create('fr_FR');

        //Campus
        $tabCampus = [];
        $nomsCampus = ['Saint-Herblain', 'Rennes', 'Paris', 'Campus En Ligne'];
        for($i=0;$i<=count($nomsCampus)-1;$i++) {
            $campus = new Campus();
            $campus->setNom($nomsCampus[$i]);
            $tabCampus[] = $campus;

            $manager->persist($campus);
            $this->addReference("campus_$i", $campus);
        }


        //Ville
        $tabVille= [];
        for ($i = 1; $i <= 10; $i ++) {
            $ville = new Ville();
            $ville->setNom($faker->unique()->city());
            $ville->setCodePostal($faker->postcode());

            $tabVille[] = $ville;
            $manager->persist($ville);

            $this->addReference("ville_$i", $ville);
        }

        //Lieu
        $tabLieu= [];
        for ($i = 1; $i <= 20; $i ++) {
            $lieu = new Lieu();
            $lieu->setNom('Lieu'.$i);
            $lieu->setRue($faker->streetAddress());
            $lieu->setLatitude($faker->latitude());
            $lieu->setLongitude($faker->longitude());
            $lieu->setVille($tabVille[mt_rand(0,9)]);

            $tabLieu[] = $lieu;
            $manager->persist($lieu);

            $this->addReference("lieu_$i", $lieu);
        }


        //Etat
        $etats = [];

        $etat1 = new Etat();
        $etat1->setLibelle("Créée");
        $etats[] = $etat1;
        $manager->persist($etat1);

        $etat2 = new Etat();
        $etat2->setLibelle("Ouverte");
        $etats[] = $etat2;
        $manager->persist($etat2);

        $etat3 = new Etat();
        $etat3->setLibelle("Clôturée");
        $etats[] = $etat3;
        $manager->persist($etat3);

        $etat4 = new Etat();
        $etat4->setLibelle("Activité en cours");
        $etats[] = $etat4;
        $manager->persist($etat4);

        $etat5 = new Etat();
        $etat5->setLibelle("Passée");
        $etats[] = $etat5;
        $manager->persist($etat5);

        $etat6 = new Etat();
        $etat6->setLibelle("Annulée");
        $etats[] = $etat6;
        $manager->persist($etat6);



        $roles = ["ROLE_USER", "ROLE_PARTICIPANT", "ROLE_ORGANISATEUR"];
        $nomsSorties = ['Piscine', 'Laser Game', 'Cinéma', 'Concert Metal', 'Accrobranche', 'Escape Game',
            'Randonnée', 'Bar', 'Boite de nuit', 'Plongée', 'Cheval', 'Zoo', 'Aqualand', 'DisneyLand',
            'Futuroscope', 'Ball Trap', 'Degustation de vins', 'Degustation Cognac', 'Visite entreprise',
            'Mont Saint-Michel', 'Bateau', 'Wake Board', 'Jet Ski', 'Parapente', 'Saut élastique', 'Musée',
            'Exposition', 'Canoe', 'Paddle', 'Ski', 'Roller', 'VTT', 'Moto', 'Skatepark', 'Concert Rap',
            'Opera', 'Ballet', 'Théâtre', 'Paintball'];
        //Participants & sorties
        $tabParticipants = [];
        //Admin
        $utilisateur = new Utilisateur();
        $utilisateur->setNom($faker->lastName());
        $utilisateur->setPrenom($faker->firstName());
        $utilisateur->setPseudo($faker->unique()->word());
        $utilisateur->setTelephone($faker->phoneNumber());
        $utilisateur->setEmail($faker->unique()->email());
        $utilisateur->setCampus($tabCampus[mt_rand(0,3)]);
        $utilisateur->setActif(true);
        $utilisateur->setAdministrateur(true);
        $utilisateur->setRoles(["ROLE_ADMIN"]);
        $utilisateur->setPassword('password');

        $tabParticipants[] = $utilisateur;
        $manager->persist($utilisateur);

        $tabSorties = [];

        foreach ($tabCampus as $campus){
            for($i=1;$i<=4;$i++) {
                $utilisateur = new Utilisateur();
                $utilisateur->setNom($faker->lastName());
                $utilisateur->setPrenom($faker->firstName());
                $utilisateur->setPseudo($faker->unique()->word());
                $utilisateur->setTelephone($faker->phoneNumber());
                $utilisateur->setEmail($faker->unique()->email());
                $utilisateur->setCampus($campus);
                $utilisateur->setActif(true);
                $utilisateur->setAdministrateur(false);
                $utilisateur->setPassword('password');

                $tabParticipants[] = $utilisateur;

                $manager->persist($utilisateur);

                for($j=1;$j<=2;$j++) {
                    $sortie = new Sortie();
                    $sortie->setNom($nomsSorties[mt_rand(0, count($nomsSorties)-1)]);
                    $sortie->setDateHeureDebut($faker->dateTimeBetween('-30 days', '+5 months'));
                    $sortie->setDuree($faker->dateTime());
                    $sortie->setDateLimitInscription($faker->dateTimeBetween('-30 days' ,$sortie->getDateHeureDebut()));
                    $sortie->setNbInscriptionsMax($faker->numberBetween(5,12));
                    $sortie->setInfosSortie($faker->text(255));
                    $sortie->setEtat($etats[mt_rand(0,5)]);
                    $sortie->setCampus($campus);
                    $sortie->setLieu($tabLieu[mt_rand(0,19)]);
                    $sortie->setOrganisateur($utilisateur);
                    $sortie->addParticipant($utilisateur);


                    $manager->persist($sortie);
                }
            }
        }


        $manager->flush();
    }
}
