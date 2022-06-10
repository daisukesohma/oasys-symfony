<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use App\Domain\Model\VilleFrance;
use App\Domain\Repository\VillesFranceRepository;
use App\Infrastructure\Config\EnvVarHelper;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function file_exists;

final class ImportVillesFranceCommand extends Command
{
    private VillesFranceRepository $villesFranceRepository;
    private EnvVarHelper $envVarHelper;

    public function __construct(VillesFranceRepository $villesFranceRepository, EnvVarHelper $envVarHelper)
    {
        $this->villesFranceRepository = $villesFranceRepository;
        $this->envVarHelper = $envVarHelper;
        parent::__construct('import:villes-france');
    }

    public function configure(): void
    {
        $this->setDescription('Imports villes france from xlsx file');
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $rootPath = $this->envVarHelper->fetch(EnvVarHelper::ROOT_PATH);
        $filepath = $rootPath . '/public/villes_france.xlsx';

        if (! file_exists($filepath)) {
            $output->writeln('File not found at ' . $filepath);

            return -1;
        }

        foreach ($this->villesFranceRepository->findAll() as $villeFrance) {
            $this->villesFranceRepository->delete($villeFrance);
        }

        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($filepath);
        $sheet = $spreadsheet->getActiveSheet();
        $count = 1;

        while (true) {
            $count++;
            $row = $sheet->rangeToArray('A' . $count . ':W' . $count);
            if (empty($row) || empty($row[0]) || empty($row[0][1])) {
                break;
            }

            $row = $row[0];

            $villeFrance = new VilleFrance(
                (string) $row[1],
                (string) $row[2],
                (string) $row[3],
                (string) $row[4],
                (string) $row[5],
            );
            $this->villesFranceRepository->save($villeFrance);
        }

        $output->writeln('Inserted ' . $count . ' villes');

        return 0;
    }
}
