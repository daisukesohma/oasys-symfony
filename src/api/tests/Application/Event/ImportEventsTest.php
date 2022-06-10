<?php

declare(strict_types=1);

namespace App\Tests\Application\Event;

use App\Application\Event\ImportEvents;
use App\Domain\Enum\EventTypeEnum;
use App\Domain\Enum\ProgramTypeEnum;
use App\Domain\Exception\InvalidEventsFile;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Event;
use App\Domain\Model\Program;
use App\Domain\Repository\EventRepository;
use App\Domain\Repository\ProgramRepository;
use App\Infrastructure\Config\EnvVarHelper;
use App\Tests\Application\ApplicationTestCase;
use Ramsey\Uuid\Uuid;
use Safe\DateTimeImmutable;
use Zend\Diactoros\UploadedFile;
use function file_exists;
use function filesize;
use function mkdir;
use function rmdir;
use function Safe\file_put_contents;
use function scandir;
use function unlink;
use const UPLOAD_ERR_OK;

class ImportEventsTest extends ApplicationTestCase
{
    protected Program $program;
    protected string $basedir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->basedir = self::$container->get(EnvVarHelper::class)->fetch(EnvVarHelper::ROOT_PATH) . 'uploads/test';
        if (! file_exists($this->basedir)) {
            mkdir($this->basedir, 0777, true);
        }

        $this->program = new Program($this->faker->name, $this->faker->name, ProgramTypeEnum::PIC);
        self::$container->get(ProgramRepository::class)->save($this->program);
    }

    protected function tearDown(): void
    {
        foreach (scandir($this->basedir) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            unlink($this->basedir . '/' . $file);
        }
        rmdir($this->basedir);

        parent::tearDown();
    }

    public function testImportEvents(): void
    {
        $file = $this->generateTestFile($this->getValidTestData($this->getCurrentUtcTime()));

        /**
         * @var Event[] $events
         */
        $events = self::$container->get(ImportEvents::class)->import(
            $this->program->getId(),
            $file,
            self::$container->get(EnvVarHelper::class)->fetch(EnvVarHelper::ROOT_PATH)
        );
        $this->assertCount(1, $events);

        $this->assertEquals($this->program->getId(), $events[0]->getProgram()->getId());
    }

    public function testInvalidProgram(): void
    {
        $file = $this->generateTestFile($this->getValidTestData($this->getCurrentUtcTime()));

        $this->expectException(NotFound::class);

        self::$container->get(ImportEvents::class)->import(
            Uuid::uuid4()->toString(),
            $file,
            self::$container->get(EnvVarHelper::class)->fetch(EnvVarHelper::ROOT_PATH)
        );
    }

    public function testDuplicateEvent(): void
    {
        $time = $this->getCurrentUtcTime();
        $event = new Event($this->faker->name, $this->faker->text, EventTypeEnum::INDIVIDUAL_SESSION);
        $event->setDateEvent($time);
        $event->setOrganizer($this->loggedUser);
        self::$container->get(EventRepository::class)->save($event);

        $this->expectException(InvalidEventsFile::class);
        $file = $this->generateTestFile($this->getValidTestData($this->getCurrentUtcTime()));
        self::$container->get(ImportEvents::class)->import(
            $this->program->getId(),
            $file,
            self::$container->get(EnvVarHelper::class)->fetch(EnvVarHelper::ROOT_PATH)
        );
    }

    protected function generateTestFile(string $data): UploadedFile
    {
        file_put_contents($this->basedir . '/test.txt', $data);

        return new UploadedFile($this->basedir . '/test.txt', filesize($this->basedir . '/test.txt'), UPLOAD_ERR_OK, 'test.txt');
    }

    private function getValidTestData(DateTimeImmutable $startTime): string
    {
        return '
Objet:	Créneau libre
Lieu:	Réunion Microsoft Teams

Début:	lun. ' . $startTime->format('d/m/Y G:i') . '
Fin:	lun. ' . $startTime->modify('+1 hour')->format('d/m/Y G:i') . '

Périodicité:	(néant)

État de la réunion:	Organisateur de la réunion

Organisateur:	Virginie Charrault - Oasys

 
________________________________________________________________________________ 
Vous êtes invité à participer à une réunion Microsoft Teams 
Nous rejoindre sur votre ordinateur ou votre appareil mobile 
Cliquez ici pour participer à la réunion <https://teams.microsoft.com/l/meetup-join/19%3ameeting_NTIwMGYyZTctYTJiYi00YjJkLWI4NmYtM2I3N2Y0ODA5OWJi%40thread.v2/0?context=%7b%22Tid%22%3a%22b293ce9d-0e05-4c0a-b11d-88dc1f070cc1%22%2c%22Oid%22%3a%222805dc87-fa07-4c60-afa5-566ab9140c8e%22%7d>  
 <https://oasys.fr/oasys/wp-content/uploads/2018/06/logo-oasys-consultants.png> 
Pour en savoir plus <https://aka.ms/JoinTeamsMeeting>  | Options de réunion <https://teams.microsoft.com/meetingOptions/?organizerId=2805dc87-fa07-4c60-afa5-566ab9140c8e&tenantId=b293ce9d-0e05-4c0a-b11d-88dc1f070cc1&threadId=19_meeting_NTIwMGYyZTctYTJiYi00YjJkLWI4NmYtM2I3N2Y0ODA5OWJi@thread.v2&messageId=0&language=fr-FR>  
';
    }

    private function getCurrentUtcTime(): DateTimeImmutable
    {
        $time = new DateTimeImmutable();
        $time = $time->modify('+2 days')->setTime(6, 0);

        return $time;
    }
}
