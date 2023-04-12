<?php

namespace App\Command;

use App\Makers\DataTransformer\SkeletonGenerator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'make:dataTransformer',
    description: 'Makes the data transformers and DTOs for a entity',
)]
class MakeDataTransformerCommand extends Command
{
    public function __construct(protected SkeletonGenerator $makerDataTransformer)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('entityName', InputArgument::REQUIRED, 'EntityName')
        ;
    }

    /**
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $maker = $this->makerDataTransformer;
        if (!$entityName = $input->getArgument('entityName')) {
            $io->error('You must be pass the entity name.');

            return Command::FAILURE;
        }

        if (!class_exists('App\Entity\\'.ucfirst($entityName))) {
            $io->warning("The class [$entityName] does not exist but the inputs and outputs will be created.");
        }

        $maker($entityName);
        $io->success('Inputs and Outputs created. Not forget add properties to DTOs');

        return Command::SUCCESS;
    }
}
