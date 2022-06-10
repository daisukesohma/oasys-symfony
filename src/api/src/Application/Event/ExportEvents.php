<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Domain\Model\Event;
use App\Domain\Repository\EventRepository;
use App\Domain\Util\Time;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Safe\DateTimeImmutable;
use function count;
use function strtoupper;

class ExportEvents
{
    use Time;

    private EventRepository $eventRepository;

    public function __construct(EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    /**
     * @return string[]
     */
    protected function getHeaderValues(): array
    {
        return [
            'Nom',
            'Prénom',
            'Adresse mail du candidat',
            'Entreprise',
            'Coach référent',
            'Transféré',
            'Spécialité du coach',
            'Nom de la prestation',
            'Statut de la prestation',
            'Durée de la prestation',
            'Date de début de la prestation',
            'Date de fin de la prestation',
            'Nom de l\'événement',
            'Date',
            'Statut de l\'événement',
        ];
    }

    /**
     * @param string[] $statusLabels
     */
    public function export(string $rootPath, array $statusLabels, ?string $search = null, ?string $status = null, ?string $organizer = null, ?string $user = null, ?string $startDate = null, ?string $endDate = null, ?string $sortColumn = 'createdAt', ?string $sortDirection = 'desc'): string
    {
        /** @var Event[] $events */
        $events = $this->eventRepository->findByFilters($search, $status, $organizer, $user, $startDate, $endDate, $sortColumn, $sortDirection);
        $export = [];

        $export[] = $this->getHeaderValues();
        foreach ($events as $event) {
            $program = $event->getProgram();
            foreach ($event->getUsers() as $user) {
                $coach = $user->getCoach();
                $company = $user->getCompany();
                $dateStart = $event->getDateEvent() ? $event->getDateEvent() : new DateTimeImmutable();
                $eventCoachSpeciality = $event->getCoachSpeciality();

                $export[] = [
                    $user->getLastName(),
                    $user->getFirstName(),
                    $user->getEmail(),
                    $company ? $company->getName() : null,
                    $coach ? $coach->getFirstName() . ' ' . strtoupper($coach->getLastName()) : null,
                    $user->getHasBeenTransferred() ? 'Oui' : 'Non',
                    $eventCoachSpeciality ? $eventCoachSpeciality->getLabel() : null,
                    $program ? $program->getName() : null,
                    $program ? $statusLabels[$program->getStatus()] : null,
                    $program ? $program->getPeriod() . ' mois' : null,
                    $program && $program->getDateStart() ? Date::PHPToExcel($program->getDateStart()) : null,
                    $program && $program->getDateEnd() ? Date::PHPToExcel($program->getDateEnd()) : null,
                    $event->getName(),
                    $dateStart ? (new DateTimeImmutable($dateStart->format('c')))->format('d/m/yy G:i') : null,
                    $statusLabels[$event->getStatus()],
                ];
            }
        }

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getActiveSheet()->fromArray($export);

        // Set the cell formatting as Date for the date columns
        for ($k = 2; $k <= count($export); $k++) {
            $spreadsheet->getActiveSheet()->getStyle('H' . ($k + 2))
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
            $spreadsheet->getActiveSheet()->getStyle('I' . ($k + 2))
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_DATE_DDMMYYYY);
            $spreadsheet->getActiveSheet()->getStyle('K' . ($k + 2))
                ->getNumberFormat()
                ->setFormatCode(NumberFormat::FORMAT_DATE_DATETIME);
        }

        $path = $rootPath . 'public/files/export.xlsx';

        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        return $path;
    }
}
