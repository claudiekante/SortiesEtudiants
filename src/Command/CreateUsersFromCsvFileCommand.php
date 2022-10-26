<?php

namespace App\Command;

use App\Entity\Utilisateur;
use App\Repository\CampusRepository;
use App\Repository\UtilisateurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CreateUsersFromCsvFileCommand extends Command
{
    private EntityManagerInterface $entityManager;

    private string $dataDirectory;

    private SymfonyStyle $io;

    private UtilisateurRepository $utilisateurRepository;
    private CampusRepository $campusRepository;
    public function __construct(
        EntityManagerInterface $entityManager,
        string $dataDirectory,
        UtilisateurRepository $utilisateurRepository,
        CampusRepository $campusRepository
    )
    {
        parent::__construct();
        $this->dataDirectory = $dataDirectory;
        $this->entityManager = $entityManager;
        $this->utilisateurRepository = $utilisateurRepository;
        $this->campusRepository = $campusRepository;
    }

    protected static $defaultName = 'app:create-users-from-file';
    protected static $defaultDescription = 'Importer des données en provenance d\'un fichier csv.';

    protected function configure(): void
    {
        $this->setDescription('Importer des données en provenance d\'un fichier csv.');


    }

    protected function initialize(InputInterface $input, OutputInterface $output) : void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->createUsers();

        return Command::SUCCESS;
    }

    // récupération du fichier csv en tableau
    private function getDataFromFile(): array
    {
        $file = $this->dataDirectory.'listeUsers.csv';

        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);

        $normalizers = [new ObjectNormalizer()];

        $encoders = [
            new CsvEncoder(),
        ];

        $serializer = new Serializer($normalizers, $encoders);

        /** @var string $fileSting */
        $fileSting = file_get_contents($file);

        $data =$serializer->decode($fileSting, $fileExtension);

        //fonction utils lors de l'utilisation fichier.xml et .yaml (besoin uniquement du tableau results)
        if (array_key_exists ('results', $data)){
            return $data ['results'];

        }
        return $data;
    }

    private function createUsers(): void
    {
        $this->io->section('CREATION DES UTILISATEUR A PARTIR D\'UN FICHIER');

        //incrémentation pour connaître le nombre d'utilisateur ajouté (- les doublons et contraintes d'unicité)
        $usersCreated = 0;

        foreach ($this->getDataFromFile() as $row){
            //vérifier si email dans row et si il n'est pas vide
            if(array_key_exists('email', $row)&& !empty($row['email'])){
                $user = $this->utilisateurRepository->findOneBy([
                    'email' => $row['email']
                    ]);

                if (!$user){
                    $user = new Utilisateur();
                    $campus = $this->campusRepository->find(12);
                    $user->setEmail($row['email'])
                        ->setPassword($row['password'])
                        ->setNom($row['nom'])
                        ->setPrenom($row['prenom'])
                        ->setCampus($campus)
                        ->setRoles(["ROLE_USER"])
                        ->setActif($row['actif'])
                        ->setAdministrateur(true)
                        ->setPseudo($row['pseudo'])
                        ->setTelephone($row ['telephone']);


                    $this->entityManager->persist($user);

                    $usersCreated++;

                }
            }
        }

        $this->entityManager->flush();

        if ($usersCreated > 1){
            $string =" {$usersCreated} UTILISATEURS CREES EN BDD.";
        }elseif ($usersCreated ===1){
            $string = "UN UTILISATEUR EST CREE EN BDD.";
        }else{
            $string = 'AUCUN UTILISATEUR N\'A ETE CREE EN BDD.';
        }
        $this->io->success($string);
    }
}
