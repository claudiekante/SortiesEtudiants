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


       foreach ($sorties as $sortieUpdate) {

           $dateFinSortie = $sortieUpdate->getDateHeureDebut()->getTimestamp() + $sortieUpdate->getDuree()->getTimestamp();

           if ($sortieUpdate->getDateHeureDebut() <= new \DateTime()) {
               if ($dateFinSortie <= $dateArchivage->getTimestamp()) {
                   //Archiver
                   $entityManager->remove($sortieUpdate);

               } elseif ($dateFinSortie <= $now->getTimestamp()
                   && $sortieUpdate->getEtat()->getLibelle() != 'Annulée'
                   && $sortieUpdate->getEtat()->getLibelle() != 'Passée') {
                   //Passée
                   $etat = $etatRepository->findByLibelle('Passée');
                   $sortieUpdate->setEtat($etat);
                   $entityManager->persist($sortieUpdate);

               } elseif ($dateFinSortie > $now->getTimestamp()
                   && $sortieUpdate->getEtat()->getLibelle() != 'Annulée'
               && $sortieUpdate->getEtat()->getLibelle() != 'Activité en cours') {
                   //Activité en Cours
                   $etat = $etatRepository->findByLibelle('Activité en cours');
                   $sortieUpdate->setEtat($etat);
                   $entityManager->persist($sortieUpdate);

               }
           } else {
               if (($sortieUpdate->getParticipant()->count() === $sortieUpdate->getNbInscriptionsMax()
                   || $sortieUpdate->getDateLimitInscription()->getTimestamp() < $now->getTimestamp())
                   && $sortieUpdate->getEtat()->getLibelle() != 'Annulée'
               && $sortieUpdate->getEtat()->getLibelle() != 'Clôturée') {

                   $etat = $etatRepository->findByLibelle('Clôturée');
                   $sortieUpdate->setEtat($etat);
                   $entityManager->persist($sortieUpdate);

               }

               if (($sortieUpdate->getParticipant()->count() < $sortieUpdate->getNbInscriptionsMax()
                   && $sortieUpdate->getDateLimitInscription()->getTimestamp() >= $now->getTimestamp())
                   && $sortieUpdate->getEtat()->getLibelle() != 'Annulée'
                   && $sortieUpdate->getEtat()->getLibelle() != 'Ouverte') {

                   $etat = $etatRepository->findByLibelle('Ouverte');
                   $sortieUpdate->setEtat($etat);
                   $entityManager->persist($sortieUpdate);

               }
           }


       }

        $entityManager->flush();

    }

    public function checkEtat(Sortie $sortie): boolean {
        return true;
    }

}