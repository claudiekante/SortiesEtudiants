<?php

namespace App\DataFixtures;

use App\Entity\Campus;
use App\Entity\Etat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        $faker = \Faker\Factory::create('fr_FR');


        //campus
        for ($i = 1; $i <= 3; $i ++) {
            $campus = new Campus();
            $campus->setNom($faker->unique()->city());
            $manager->persist($campus);
            $this->addReference("campus_$i", $campus);
        }



        //Etat
        $etat1 = new Etat();
        $etat1->setLibelle("Créée");
        $manager->persist($etat1);

        $etat2 = new Etat();
        $etat2->setLibelle("Ouverte");
        $manager->persist($etat2);

        $etat3 = new Etat();
        $etat3->setLibelle("Clôturée");
        $manager->persist($etat3);

        $etat4 = new Etat();
        $etat4->setLibelle("Activité en cours");
        $manager->persist($etat4);

        $etat5 = new Etat();
        $etat5->setLibelle("Passée");
        $manager->persist($etat5);

        $etat6 = new Etat();
        $etat6->setLibelle("Annulée");
        $manager->persist($etat6);

        $manager->flush();
    }
}
