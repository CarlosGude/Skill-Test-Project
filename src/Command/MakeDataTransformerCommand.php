<?php

namespace App\Command;

use App\MakerDataTransformer\MakerDataTransformer;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'make:dataTransformer',
    description: 'Add a short description for your command',
)]
class MakeDataTransformerCommand extends Command
{

    public function __construct(protected  MakerDataTransformer $makerDataTransformer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('entityName', InputArgument::REQUIRED, 'EntityName')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $entityName = $input->getArgument('entityName');

        if (!$entityName) {
            $io->error('You must be pass the entity name.');

            return Command::FAILURE;
        }

        $this->makerDataTransformer->generateClass($entityName);
        $io->success('Inputs and Outputs created. Not forget add properties to DTOs');

        return Command::SUCCESS;
    }
}
