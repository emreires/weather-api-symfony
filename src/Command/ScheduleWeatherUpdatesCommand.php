<?php

namespace App\Command;

use App\Entity\FavoriteCity;
use App\Message\UpdateWeatherData;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class ScheduleWeatherUpdatesCommand extends Command
{
    protected static $defaultName = 'app:schedule-weather-updates';

    private $entityManager;
    private $messageBus;
    private $io;

    public function __construct(
        EntityManagerInterface $entityManager,
        MessageBusInterface $messageBus
    ) {
        parent::__construct();
        $this->entityManager = $entityManager;
        $this->messageBus = $messageBus;
    }

    protected function configure()
    {
        $this
            ->setDescription('Schedules weather data updates for all favorite cities')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->io = new SymfonyStyle($input, $output);
        $this->io->title('Starting weather data update scheduling');

        $favoriteCities = $this->entityManager->getRepository(FavoriteCity::class)->findAll();
        $count = 0;

        foreach ($favoriteCities as $city) {
            $this->messageBus->dispatch(new UpdateWeatherData(
                $city->getName(),
                $city->getCountryCode()
            ));
            $count++;
        }

        $this->io->success(sprintf('Successfully scheduled updates for %d cities', $count));
        return Command::SUCCESS;
    }
} 