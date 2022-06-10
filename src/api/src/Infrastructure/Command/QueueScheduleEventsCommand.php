<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use App\Application\Event\ArchiveEvent;
use App\Application\Event\FinishEvent;
use App\Application\Queue\QueueMessagingManager;
use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use App\Domain\Repository\EventRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function count;

final class QueueScheduleEventsCommand extends Command
{
    private EventRepository $eventRepository;
    private QueueMessagingManager $queueMessagingManager;
    private ArchiveEvent $archiveEvent;
    private FinishEvent $finishEvent;

    public function __construct(EventRepository $eventRepository, QueueMessagingManager $queueMessagingManager, ArchiveEvent $archiveEvent, FinishEvent $finishEvent)
    {
        $this->eventRepository = $eventRepository;
        $this->queueMessagingManager = $queueMessagingManager;
        $this->archiveEvent = $archiveEvent;
        $this->finishEvent = $finishEvent;
        parent::__construct('queue:schedule-events');
    }

    public function configure(): void
    {
        $this->setDescription('Queues future events into Redis for processing');
    }

    /**
     * @throws InvalidDateValue
     * @throws InvalidStringValue
     * @throws NotFound
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $events = $this->eventRepository->getEventsToQueue();
        foreach ($events as $event) {
            $output->writeln('Queuing Event: ' . $event->getId() . ', current status: ' . $event->getStatus());
            $this->queueMessagingManager->queue($event->getId());
        }
        $output->writeln('Queued ' . count($events) . ' events');

        return 0;
    }
}
