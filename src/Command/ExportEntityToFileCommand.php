<?php

namespace App\Command;

use App\Services\ExportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'exportEntityToFile',
    description: 'Add a short description for your command',
)]
class ExportEntityToFileCommand extends Command
{
    private ExportService $exportService;
    public function __construct(ExportService $exportService)
    {
        $this->exportService = $exportService;

        parent::__construct();
    }
    protected function configure(): void
    {
        $this
            ->setDescription('Export data of a specific entity')
            ->addArgument('entity', InputArgument::REQUIRED, 'Entity class to export')
            ->addArgument('format', InputArgument::OPTIONAL, 'Format to export (json, xml, csv)', 'csv');
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $entity = $input->getArgument('entity');
        $format = $input->getArgument('format');

        try {

            $result = $this->exportService->formatContent($entity, $format);
            $io->success(sprintf('Successfully created export file: %s', $result['file']));

        } catch (\InvalidArgumentException $e) {
            $io->error('Invalid argument: ' . $e->getMessage());
        } catch (\Exception $e) {
            $io->error('Error: ' . $e->getMessage());
        }

        return Command::SUCCESS;
    }
}
