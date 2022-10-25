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
               if ($dateFinSortie <= $dateArchivage->getTimestamp()
                   && $sortieUpdate->getEtat()->getLibelle() != 'Archivée') {
                   //Archiver
                   $etat = $etatRepository->findByLibelle('Archivée');
                   $sortieUpdate->setEtat($etat);
                   $entityManager->persist($sortieUpdate);

               } elseif ($dateFinSortie <= $now->getTimestamp()
                   && $sortieUpdate->getEtat()->getLibelle() != 'Annulée'
                   && $sortieUpdate->getEtat()->getLibelle() != 'Passée'
                   && $sortieUpdate->getEtat()->getLibelle() != 'Archivée') {
                   //Passée
                   $etat = $etatRepository->findByLibelle('Passée');
                   $sortieUpdate->setEtat($etat);
                   $entityManager->persist($sortieUpdate);

               } elseif ($dateFinSortie > $now->getTimestamp()
                   && $sortieUpdate->getEtat()->getLibelle() != 'Annulée'
               && $sortieUpdate->getEtat()->getLibelle() != 'Activité en cours'
                   && $sortieUpdate->getEtat()->getLibelle() != 'Archivée') {
                   //Activité en Cours
                   $etat = $etatRepository->findByLibelle('Activité en cours');
                   $sortieUpdate->setEtat($etat);
                   $entityManager->persist($sortieUpdate);

               }
           } else {
               if (($sortieUpdate->getParticipant()->count() === $sortieUpdate->getNbInscriptionsMax()
                   || $sortieUpdate->getDateLimitInscription()->getTimestamp()+86400 < $now->getTimestamp())
                   && $sortieUpdate->getEtat()->getLibelle() != 'Annulée'
               && $sortieUpdate->getEtat()->getLibelle() != 'Clôturée'
                   && $sortieUpdate->getEtat()->getLibelle() != 'Archivée') {

                   $etat = $etatRepository->findByLibelle('Clôturée');
                   $sortieUpdate->setEtat($etat);
                   $entityManager->persist($sortieUpdate);

               }

               if (($sortieUpdate->getParticipant()->count() < $sortieUpdate->getNbInscriptionsMax()
                   && $sortieUpdate->getDateLimitInscription()->getTimestamp()+86400 >= $now->getTimestamp())
                   && $sortieUpdate->getEtat()->getLibelle() != 'Annulée'
                   && $sortieUpdate->getEtat()->getLibelle() != 'Ouverte'
                   && $sortieUpdate->getEtat()->getLibelle() != 'Créée'
                   && $sortieUpdate->getEtat()->getLibelle() != 'Archivée') {

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