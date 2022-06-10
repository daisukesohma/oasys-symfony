<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Domain\Enum\EventMeetingEnum;
use App\Domain\Enum\EventTypeEnum;
use App\Domain\Enum\ProgramTypeEnum;
use App\Domain\Repository\EventRepository;
use App\Domain\Util\Time;
use function array_map;
use function count;
use function implode;
use function str_replace;

final class ExportEventsCrossTalent
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
            'Type d\'événement',
            'Quelle modèle souhaitez vous utiliser ?',
            'Prestation',
            'Nom',
            'Début',
            'Fin',
            'Modalité',
            'Memo',
            'Organisateur',
            'Invités',
        ];
    }

    public function export(?string $search = null, ?string $status = null, ?string $organizer = null, ?string $user = null, ?string $startDate = null, ?string $endDate = null, ?string $sortColumn = 'createdAt', ?string $sortDirection = 'desc'): string
    {
        $events = $this->eventRepository->findByFilters($search, $status, $organizer, $user, $startDate, $endDate, $sortColumn, $sortDirection, EventTypeEnum::INDIVIDUAL_SESSION);
        $rows = [$this->getHeaderValues()];

        foreach ($events as $event) {
            $program = $event->getProgram();
            $startTime = $this->convertTime($event->getDateEvent());
            $endTime = $this->convertTime($event->getDateEventEnd());
            $organizer = $event->getOrganizer();
            $user = count($event->getUsers()) > 0 ? $event->getUsers()[0] : null;

            $rows[] = [
                'Séance individuelle',
                $program !== null && $program->getType() === ProgramTypeEnum::PIC ? 'PIC' : 'EIC',
                $program !== null ? $program->getName() : '',
                $event->getName(),
                $startTime->format('d/m/Y G:i:s'),
                $endTime->format('d/m/Y G:i:s'),
                $event->getType() === EventMeetingEnum::PRESENTIAL ? 'Présentiel' : 'Visio',
                str_replace(["\r", "\n", "\t", ',', '.', ';', ':', '<', '>'], ' ', $event->getMemo() ?? ''),
                $organizer !== null ? $organizer->getId() : '',
                $user !== null ? $user->getId() : '',
            ];
        }

        return implode(
            "\n",
            array_map(
                static fn (array $row) => implode(
                    ',',
                    array_map(static fn (?string $column) => $column === null ? '' : str_replace(',', '\\,', $column), $row)
                ),
                $rows
            )
        );
    }
}
