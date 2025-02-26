<?php

namespace App\Repository;

use App\Entity\Sortie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Sortie>
 *
 * @method Sortie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Sortie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Sortie[]    findAll()
 * @method Sortie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SortieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Sortie::class);
    }

    public function add(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Sortie $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function listSortie()
    {
        $query = $this
            ->createQueryBuilder('s')
            ->select('s','p','o', 'e')
            ->leftJoin('s.Organisateur', 'o')
            ->leftJoin('s.etat', 'e')
            ->leftJoin('s.participant','p')
            ->orderBy('s.dateHeureDebut','ASC')
            ->getQuery()
            ->getResult();
        return $query;
    } // -- listSortie()

    public function findOneSortie($id) {
        $query = $this->createQueryBuilder('s')
            ->select('s','c','l','v','o','p')
            ->innerJoin('s.Organisateur','o')
            ->innerJoin('s.campus','c')
            ->innerJoin('s.lieu', 'l')
            ->innerJoin('l.ville', 'v')
            ->innerJoin('s.participant','p')
            ->andWhere('s.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
        return $query;
    }

    public function search($id, $mots = null , $campus = null, $ouvertes = false, $organisateur = false,
                           $inscrit = false, $pasInscrit = false, $dejaPasse = false,
                           $dateHeureDebut = null, $dateLimiteInscription = null
    )
    {
        $query = $this->createQueryBuilder('s');

        if ($mots != null){
            $query->where('MATCH_AGAINST (s.nom, s.infosSortie) AGAINST (:mots boolean)>0')->setParameter('mots', $mots);
        }

        if ($campus != null)
        {
            $query->leftJoin('s.campus', 'c');
            $query->andWhere('c.id = :campus')->setParameter('campus', $campus);
        }

        if($ouvertes && $dejaPasse) {
            $query->leftJoin('s.etat','e');
            $query->andWhere('e.libelle = :ouverte ');
            $query->orWhere('e.libelle = :passee ');
            $query->setParameter('ouverte', 'Ouverte');
            $query->setParameter('passee', 'Passée');

        } else {
            if ($ouvertes) {
                $query->leftJoin('s.etat', 'e');
                $query->andWhere('e.libelle = :ouverte ');
                $query->setParameter('ouverte', 'Ouverte');
            }

            if ($dejaPasse) {
                $query->leftJoin('s.etat', 'e');
                $query->andWhere('e.libelle = :passee ');
                $query->setParameter('passee', 'Passée');
            }
        }

        if ($organisateur)
        {
            $query->andWhere('s.Organisateur = :organisateur')->setParameter('organisateur', $id);
        }

        if(!$inscrit || !$pasInscrit) {

            if ($inscrit) {
                $query->innerJoin('s.participant', 'p');
                $query->andWhere('p.id = :id')->setParameter('id', $id);
            }

            if ($pasInscrit) {
                $query->innerJoin('s.participant', 'p');
                $query->andWhere('p.id != :id')->setParameter('id', $id);
            }

        }


        if ($dateHeureDebut != null)
        {
            $query->andWhere('s.dateHeureDebut > :dateHeureDebut')->setParameter('dateHeureDebut',  $dateHeureDebut);
        }

        if ($dateLimiteInscription != null)
        {
            $query->andWhere('s.dateHeureDebut < :dateLimite')
                ->setParameter('dateLimite', $dateLimiteInscription);
        }

        $query->orderBy('s.dateHeureDebut','ASC');
        return $query->getQuery()->getResult();
    } // -- search()

//    /**
//     * @return Sortie[] Returns an array of Sortie objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('s.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Sortie
//    {
//        return $this->createQueryBuilder('s')
//            ->andWhere('s.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
