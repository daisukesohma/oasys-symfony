<?php

declare(strict_types=1);

namespace App\Infrastructure\Command;

use App\Application\Event\ArchiveEvent;
use App\Application\Event\FinishEvent;
use App\Application\Event\NotifyUpcomingEvent;
use App\Application\Event\StartEvent;
use App\Application\Program\RemindFinishProgram;
use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\NotFound;
use RedisClient\RedisClient;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use function Safe\json_decode;
use function time;

final class QueueScheduledWorkerCommand extends Command
{
    public const TIME_WINDOW = 120;

    private RedisClient $redisClient;
    private StartEvent $startEvent;
    private ArchiveEvent $archiveEvent;
    private FinishEvent $finishEvent;
    private NotifyUpcomingEvent $notifyUpcomingEvent;
    private RemindFinishProgram $remindFinishProgram;

    public function __construct(
        RedisClient $redisClient,
        StartEvent $startEvent,
        ArchiveEvent $archiveEvent,
        FinishEvent $finishEvent,
        NotifyUpcomingEvent $notifyUpcomingEvent,
        RemindFinishProgram $remindFinishProgram
    ) {
        $this->redisClient = $redisClient;
        $this->startEvent = $startEvent;
        $this->archiveEvent = $archiveEvent;
        $this->finishEvent = $finishEvent;
        $this->notifyUpcomingEvent = $notifyUpcomingEvent;
        $this->remindFinishProgram = $remindFinishProgram;
        parent::__construct('queue:scheduled-worker');
    }

    public function configure(): void
    {
        $this->setDescription('Long running process, processes all queue items');
    }

    /**
     * @throws NotFound
     * @throws InvalidDateValue
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $messages = $this->redisClient->zrangebyscore('queue', time() - self::TIME_WINDOW, time());
        $output->writeln('Processing queued messages');
        foreach ($messages as $message) {
            $output->writeln('Processing message: ' . $message);

            try {
                $scheduledMessage = json_decode($message, true);
                switch ($scheduledMessage['useCase']) {
                    case StartEvent::class:
                        $this->startEvent->start($scheduledMessage['id']);
                        break;
                    case ArchiveEvent::class:
                        $this->archiveEvent->archive($scheduledMessage['id']);
                        break;
                    case FinishEvent::class:
                        $this->finishEvent->finish($scheduledMessage['id']);
                        break;
                    case RemindFinishProgram::class:
                        $this->remindFinishProgram->notify($scheduledMessage['id']);
                        break;
                    case NotifyUpcomingEvent::class:
                        $this->notifyUpcomingEvent->notify($scheduledMessage['id']);
                        break;
                    default:
                        $continue = false;
                }
                $this->redisClient->zrem('queue', $message);
                $output->writeln('Message processed');
            } catch (InvalidDateValue | NotFound $e) {
                $output->writeln('Error thrown by message worker: ' . $e->getMessage());
                // todo: hide trace output for production when we implement proper logging
                $output->writeln($e->getTraceAsString());
            }
        }

        return 0;
    }
}
