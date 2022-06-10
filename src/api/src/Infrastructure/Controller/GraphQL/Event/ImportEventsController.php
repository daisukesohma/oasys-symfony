<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Event;

use App\Application\Event\ImportEvents;
use App\Domain\Exception\EventExistsAtTimeException;
use App\Domain\Exception\InvalidData;
use App\Domain\Exception\InvalidDateValue;
use App\Domain\Exception\InvalidEventsFile;
use App\Domain\Exception\InvalidFileValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Infrastructure\Config\EnvVarHelper;
use Psr\Http\Message\UploadedFileInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class ImportEventsController extends AbstractController
{
    private ImportEvents $importEvents;
    private EnvVarHelper $envVarHelper;

    public function __construct(ImportEvents $importEvents, EnvVarHelper $envVarHelper)
    {
        $this->importEvents = $importEvents;
        $this->envVarHelper = $envVarHelper;
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
     *
     * @Mutation
     * @Logged
     * @Right("ROLE_CREATE_EVENT")
     */
    public function importEvents(string $programId, UploadedFileInterface $file): array
    {
        return $this->importEvents->import($programId, $file, $this->envVarHelper->fetch(EnvVarHelper::ROOT_PATH));
    }
}
