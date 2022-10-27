<?php

namespace App\Services;

use App\Entity\Sortie;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use phpDocumentor\Reflection\Types\Boolean;

class EtatService
{


    public function updateEtatSortie(SortieRepository $sortieRepository, EtatRepository $etatRepository, EntityManagerInterface $entityManager): void {

       $sorties= $sortieRepository->listSortie();
       $dateArchivage = new \DateTime('-1 month');
       $now = new \DateTime();


       foreach ($sorties as $sortie) {

           $dateFinSortie = $sortie->getDateHeureDebut()->getTimestamp() + $sortie->getDuree()->getTimestamp();

           if ($sortie->getDateHeureDebut() <= new \DateTime()) { //dateHeureDébutPassée
               if ($dateFinSortie <= $dateArchivage->getTimestamp()
                   && $sortie->getEtat()->getLibelle() != 'Archivée') {
                   //Archiver
                   $etat = $etatRepository->findByLibelle('Archivée');
                   $sortie->setEtat($etat);
                   $entityManager->persist($sortie);

               } elseif ($dateFinSortie <= $now->getTimestamp()
                   && $sortie->getEtat()->getLibelle() != 'Annulée'
                   && $sortie->getEtat()->getLibelle() != 'Passée'
                   && $sortie->getEtat()->getLibelle() != 'Archivée') {
                   //Passée
                   $etat = $etatRepository->findByLibelle('Passée');
                   $sortie->setEtat($etat);
                   $entityManager->persist($sortie);

               } elseif ($dateFinSortie > $now->getTimestamp()
                   && $sortie->getEtat()->getLibelle() != 'Annulée'
                   && $sortie->getEtat()->getLibelle() != 'Activité en cours'
                   && $sortie->getEtat()->getLibelle() != 'Archivée'
                   && $sortie->getEtat()->getLibelle() != 'Créée') {
                   //Activité en Cours
                   $etat = $etatRepository->findByLibelle('Activité en cours');
                   $sortie->setEtat($etat);
                   $entityManager->persist($sortie);

               }
           } else {
               if (($sortie->getParticipant()->count() === $sortie->getNbInscriptionsMax()
                   || $sortie->getDateLimitInscription()->getTimestamp()+86400 < $now->getTimestamp())
                   && $sortie->getEtat()->getLibelle() != 'Annulée'
                   && $sortie->getEtat()->getLibelle() != 'Clôturée'
                   && $sortie->getEtat()->getLibelle() != 'Créée') {

                   $etat = $etatRepository->findByLibelle('Clôturée');
                   $sortie->setEtat($etat);
                   $entityManager->persist($sortie);

               }

               if (($sortie->getParticipant()->count() < $sortie->getNbInscriptionsMax()
                   && $sortie->getDateLimitInscription()->getTimestamp()+86400 >= $now->getTimestamp())
                   && $sortie->getEtat()->getLibelle() != 'Annulée'
                   && $sortie->getEtat()->getLibelle() != 'Ouverte'
                   && $sortie->getEtat()->getLibelle() != 'Créée') {

                   $etat = $etatRepository->findByLibelle('Ouverte');
                   $sortie->setEtat($etat);
                   $entityManager->persist($sortie);

               }
           }


       }

        $entityManager->flush();

    }

    public function checkEtat(Sortie $sortie): boolean {
        return true;
    }

}