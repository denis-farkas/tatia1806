<?php

namespace App\Command;

use App\Entity\Cours;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;


class AppCreateCoursCommand extends Command
{
    protected static $defaultName = 'app:create-cours';
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        parent::__construct();
        $this->entityManager = $entityManager;
    }
    
    protected function configure()
    {
        $this
            ->setName('app:create-cours')
            ->setDescription('Create new cours');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $coursArray = [
            [
                'name' => 'Eveil',
                'age' => '4-5 ans',
                'description' => 'Ce cours est destiné aux enfants en moyenne/grande section.',
                'salle' => 'Palis Bleu',
                'price' => 180,
                'day' => 'Mercredi',
                'schedule' => '10h15 à 11h',
                'start_hour' => '10:15',
                'end_hour' => '11:00',
            ],
            [
                'name' => 'Eveil',
                'age' => '4-5 ans',
                'description' => 'Ce cours est destiné aux enfants en moyenne/grande section.',
                'salle' => 'Village des enfants',
                'price' => 180,
                'day' => 'Samedi',
                'schedule' => '14h15 à 15h00',
                'start_hour' => '14:15',
                'end_hour' => '15:00',
            ],
            [
                'name' => 'Initiation',
                'age' => '6-7 ans',
                'description' => 'Ce cours est destiné aux enfants en CP/CE1.',
                'salle' => 'Palis Bleu',
                'price' => 190,
                'day' => 'Mercredi',
                'schedule' => '11h00 à 12h00',
                'start_hour' => '11:00',
                'end_hour' => '12:00',
            ],
            [
                'name' => 'Initiation',
                'age' => '6-7 ans',
                'description' => 'Ce cours est destiné aux enfants en CP/CE1.',
                'salle' => 'Centre associatif',
                'price' => 190,
                'day' => 'Mercredi',
                'schedule' => '13h45 à 14h45',
                'start_hour' => '13:45',
                'end_hour' => '14:45',
            ],
            [
                'name' => 'Initiation',
                'age' => '6-7 ans',
                'description' => 'Ce cours est destiné aux enfants en CP/CE1.',
                'salle' => 'Village des enfants',
                'price' => 190,
                'day' => 'Samedi',
                'schedule' => '15h00 à 16h00',
                'start_hour' => '15:00',
                'end_hour' => '16:00',
            ],
            [
                'name' => 'Classique',
                'age' => 'À partir de 8 ans',
                'description' => 'Cours de classique',
                'salle' => 'Centre associatif',
                'price' => 200,
                'day' => 'Mercredi',
                'schedule' => '14h45 à 15h45',
                'start_hour' => '14:45',
                'end_hour' => '15:45',
            ],
            [
                'name' => 'Classique',
                'age' => 'Ados/Adultes',
                'description' => 'Cours de classique',
                'salle' => 'Village des enfants',
                'price' => 200,
                'day' => 'Samedi',
                'schedule' => '16h00 à 17h15',
                'start_hour' => '16:00',
                'end_hour' => '17:15',
            ],
            [
                'name' => 'Contemporain',
                'age' => 'À partir de 8 ans',
                'description' => 'Cours de contemporain',
                'salle' => 'Centre associatif',
                'price' => 200,
                'day' => 'Mercredi',
                'schedule' => '15h45 à 16h45',
                'start_hour' => '15:45',
                'end_hour' => '16:45',
            ],
            [
                'name' => 'Contemporain',
                'age' => 'Ados/Adultes',
                'description' => 'Cours de contemporain',
                'salle' => 'Village des enfants',
                'price' => 200,
                'day' => 'Samedi',
                'schedule' => '17h15 à 18h30',
                'start_hour' => '17:15',
                'end_hour' => '18:30',
            ],
        ];

        foreach ($coursArray as $data) {
            $cours = new Cours();
            $cours->setName($data['name'])
                ->setAge($data['age'])
                ->setDescription($data['description'])
                ->setSalle($data['salle'])
                ->setPrice($data['price'])
                ->setDay($data['day'])
                ->setSchedule($data['schedule'])
                ->setStartHour(\DateTimeImmutable::createFromFormat('H:i', $data['start_hour']))
                ->setEndHour(\DateTimeImmutable::createFromFormat('H:i', $data['end_hour']));
            $this->entityManager->persist($cours);
        }

        $this->entityManager->flush();

        $output->writeln('<info>Tous les cours ont été ajoutés à la base de données.</info>');

        return Command::SUCCESS;
    }
}