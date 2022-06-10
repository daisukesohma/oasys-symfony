<?php

declare(strict_types=1);

namespace App\Application\Event;

use App\Application\File\ImportFile;
use App\Domain\Enum\EventMeetingEnum;
use App\Domain\Enum\EventTypeEnum;
use App\Domain\Exception\EventExistsAtTimeException;
use App\Domain\Exception\InvalidData;
use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\InvalidEventsFile;
use App\Domain\Exception\InvalidFileValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Repository\EventRepository;
use App\Domain\Repository\ProgramRepository;
use App\Domain\Repository\UserRepository;
use Psr\Http\Message\UploadedFileInterface;
use Safe\DateTimeImmutable;
use function explode;
use function Safe\file_get_contents;
use function Safe\iconv;
use function Safe\preg_match;
use function strpos;
use function strtolower;
use function trim;

class ImportEvents
{
    use ImportFile;

    private EventRepository $eventRepository;
    private CreateEvent $createEvent;
    private UserRepository $userRepository;
    private ProgramRepository $programRepository;

    private const KEY_MAP = [
        'objet' => 'object',
        'lieu' => 'meetingPlace',
        'début' => 'startTime',
        'fin' => 'endTime',
        'périodicité' => 'recurring',
        'état de la réunion' => 'meetingPlace',
        'organisateur' => 'organiser',
        'participants obligatoires' => 'mandatoryParticipants',
        'cliquez ici pour participer à la réunion' => 'meetingLink',
    ];

    private const MEETING_PLACE_VALUE_ONLINE = 'Réunion Microsoft Teams';
    private const NULL_VALUE = '(néant)';

    public function __construct(
        EventRepository $eventRepository,
        CreateEvent $createEvent,
        UserRepository $userRepository,
        ProgramRepository $programRepository
    ) {
        $this->eventRepository = $eventRepository;
        $this->createEvent = $createEvent;
        $this->userRepository = $userRepository;
        $this->programRepository = $programRepository;
    }

    /**
     * @return Event[]
     *
     * @throws InvalidEventsFile
     * @throws InvalidFileValue
     * @throws NotFound
     * @throws InvalidData
     * @throws InvalidDateValue
     * @throws EventExistsAtTimeException
     */
    public function import(string $programId, UploadedFileInterface $file, string $rootPath): array
    {
        $program = $this->programRepository->mustFindOneById($programId);
        $events = $this->parseImportFile($this->readFile($this->importFile($file, $rootPath)));
        $eventChains = [];
        $loggedUser = $this->userRepository->getLoggedUser();
        $errors = [];
        $return = [];

        foreach ($events as $k => $event) {
            if (empty($event['object']) || empty($event['startTime']) || empty($event['endTime'])) {
                continue;
            }

            $startTime = $this->parseTime($event['startTime']);
            $endTime = $this->parseTime($event['endTime']);
            $existingEvent = $this->eventRepository->getEventBetween($startTime, $endTime, $loggedUser);

            if ($existingEvent !== null) {
                $errors[] = [
                    'error' => 'event_exists',
                    'variable' => [
                        'eventName' => $existingEvent->getName(),
                        'eventDate' => $existingEvent->getDateEvent(),
                        'eventDateEnd' => $existingEvent->getDateEventEnd(),
                    ],
                ];
                continue;
            }

            if (! isset($event['meetingPlace']) || ($event['meetingPlace'] === self::MEETING_PLACE_VALUE_ONLINE && ! isset($event['meetingLink']))) {
                $errors[] = [
                    'error' => 'event_missing_meeting',
                    'variable' => [
                        'eventName' =>  $event['object'],
                    ],
                ];
                continue;
            }

            if ($event['recurring'] !== self::NULL_VALUE) {
                if (! isset($eventChains[$startTime->getTimestamp()])) {
                    $eventChains[$startTime->getTimestamp()] = 1;
                } else {
                    $timestamp = $startTime->getTimestamp();
                    $startTime = $startTime->modify('+' . $eventChains[$timestamp] . ' days');
                    $endTime = $endTime->modify('+' . $eventChains[$timestamp] . ' days');

                    $eventChains[$timestamp]++;
                }
            }

            $events[$k]['startTime'] = $startTime;
            $events[$k]['endTime'] = $endTime;
        }

        if (! empty($errors)) {
            throw new InvalidEventsFile($errors);
        }

        foreach ($events as $event) {
            $startTime = $event['startTime'];
            $endTime = $event['endTime'];

            $return[] = $this->createEvent->create(
                $event['object'],
                'Créé à partir d\'événements importés',
                EventTypeEnum::INDIVIDUAL_SESSION,
                [],
                $this->userRepository->getLoggedUser()->getId(),
                $startTime->format('Y-m-d H:i:s'),
                $endTime->format('Y-m-d H:i:s'),
                null,
                $program->getId(),
                $event['meetingPlace'] === self::MEETING_PLACE_VALUE_ONLINE ? $event['meetingLink'] : null,
                $event['meetingPlace'] === self::MEETING_PLACE_VALUE_ONLINE ? EventMeetingEnum::VISIO : EventMeetingEnum::PRESENTIAL,
                $event['meetingPlace'] === self::MEETING_PLACE_VALUE_ONLINE ? null : $event['meetingPlace'],
                null,
                null,
                true
            );
        }

        return $return;
    }

    /**
     * @return mixed[]
     */
    private function parseImportFile(string $content): array
    {
        $lines = explode("\n", $content);
        $events = [];
        $k = -1;

        foreach ($lines as $line) {
            $line = trim($line);
            if (empty($line)) {
                continue;
            }

            preg_match('/(.*?)\s*[:<]\s*([^>]*)/', $line, $matches);
            if (! isset($matches[2]) || ! isset(self::KEY_MAP[strtolower($matches[1])])) {
                continue;
            }
            $key = self::KEY_MAP[strtolower($matches[1])];
            $value = $matches[2];

            if ($key === 'object') {
                $k++;
                $events[$k] = [];
            }

            $events[$k][$key] = $value;
        }

        return $events;
    }

    private function parseTime(string $timeString): DateTimeImmutable
    {
        preg_match('/([a-zA-Z0-9]+\.\s)?([\d:\s\/]+)/', $timeString, $matches);

        return DateTimeImmutable::createFromFormat('d/m/Y G:i', $matches[2]);
    }

    /**
     * @throws InvalidEventsFile
     */
    private function readFile(string $filepath): string
    {
        $content = file_get_contents($filepath);
        if (! $content) {
            throw new InvalidEventsFile([[
                'error' => 'files_invalid',
                'variable' => '',
            ],
            ]);
        }

        // Sometimes file_get_contents or the file itself can mess up the encoding while reading the file
        // Try to convert it to the correct encoding
        if (strpos(strtolower((string) $content), 'début') === false) {
            $content = iconv('ISO-8859-1', 'UTF-8//TRANSLIT', $content);
            if (strpos(strtolower((string) $content), 'début') === false) {
                throw new InvalidEventsFile([[
                    'error' => 'files_invalid',
                    'variable' => '',
                ],
                ]);
            }
        }

        return $content;
    }
}
