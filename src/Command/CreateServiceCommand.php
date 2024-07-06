<?php

namespace App\Command;

use App\Entity\OAuthClient;
use App\Entity\ServiceProvider;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:create-service',
    description: 'Creates a new OAuth service',
)]
class CreateServiceCommand extends Command
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    )
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setDescription('Creates a new service with an associated OAuth client')
            ->addArgument('serviceName', InputArgument::REQUIRED, 'The name of the service');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $serviceName = $input->getArgument('serviceName');

        $client = new OAuthClient();
        $this->entityManager->persist($client);

        $service = new ServiceProvider();
        $service->setName($serviceName)->setClient($client);
        $this->entityManager->persist($service);

        $this->entityManager->flush();

        $output->writeln('Service and OAuth client created successfully!');
        $output->writeln('Client ID: ' . $client->getId());
        $output->writeln('Client Secret: ' . $client->getSecret());

        return Command::SUCCESS;
    }
}
