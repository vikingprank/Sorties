<?php

namespace App\Command;

use App\Entity\User;
use App\Entity\Campus;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;
use Symfony\Component\Serializer\Encoder\CsvEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class CreateUsersFromCsvCommand extends Command
{
    private EntityManagerInterface $entityManager;

    private SymfonyStyle $io;

    private string $dataDirectory;

    private UserRepository $userRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        string $dataDirectory,
        UserRepository $userRepository
    )
    {
        parent::__construct();
        $this->dataDirectory = $dataDirectory;
        $this->entityManager = $entityManager;
        $this->userRepository = $userRepository;
    }

    protected static $defaultName = 'app:create-user-from-file';

    protected function configure(): void
    {
        $this->setDescription('Apporter des fichiers depuis un fichier csv');
    }

    protected function initialize(InputInterface $input, OutputInterface $output): void
    {
        $this->io = new SymfonyStyle($input, $output);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->createUsers();
        return Command::SUCCESS;
    }

    private function getDataFromFile(): array
    {
        $file = $this->dataDirectory . 'users.csv';

        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);

        $normalizers = [new ObjectNormalizer()];

        $encoders = [
            new CsvEncoder(),
            new XmlEncoder()
        ];

        $serializer =  new Serializer($normalizers, $encoders);

        /** @var string $fileString */
        $fileString =  file_get_contents($file);

        $data = $serializer->decode($fileString, $fileExtension);
        dd($data);
        return $data;
    }

    private function createUsers(): void
    {
        $this->io->section('CREATION DES UTILISATEURS A PARTIR DU FICHIER');

        foreach($this->getDataFromFile() as $row)
        {
                    $user = new User();
                    //$campus = new Campus();
                    //$campus = 'Lyon';

                    $user->setEmail($row['email'])
                         ->setPassword('randomPassword')
                         ->setNom($row['nom'])   
                         ->setPrenom($row['prenom'])   
                         ->setTelephone($row['telephone'])   
                         ->setPseudo($row['pseudo']) 
                         //->setCampus(null)
                         ->setAdministrateur(0)
                         ->setActif(1)
                         ->setRoles([ "ROLE_USER" ]);
                    $this->entityManager->persist($user);     
            //var_dump($user);
        }
        
        $this->entityManager->flush();
    }
}
