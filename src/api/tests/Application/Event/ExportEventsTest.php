<?php

declare(strict_types=1);

namespace App\Tests\Application\Event;

use App\Application\Event\ExportEvents;
use App\Application\Event\ExportEventsCrossTalent;
use App\Domain\Enum\EventStatusEnum;
use App\Domain\Enum\EventTypeEnum;
use App\Domain\Enum\ProgramStatusEnum;
use App\Domain\Enum\ProgramTypeEnum;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Model\Company;
use App\Domain\Model\Event;
use App\Domain\Model\Program;
use App\Domain\Model\User;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\EventRepository;
use App\Domain\Repository\ProgramRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;
use App\Infrastructure\Config\EnvVarHelper;
use App\Tests\Application\ApplicationTestCase;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use Ramsey\Uuid\Uuid;
use function array_filter;
use function array_merge;
use function count;
use function rand;

class ExportEventsTest extends ApplicationTestCase
{
    /** @var Program[] */
    private array $testPrograms;
    private int $expectedRows;

    /** @var string[] */
    private array $statusLabels;

    protected function setUp(): void
    {
        parent::setUp();

        $company = new Company($this->faker->name, 'TEST_COMPANY_1');
        self::$container->get(CompanyRepository::class)->save($company);

        $eventTypes = [];
        $this->expectedRows = 0;
        $this->testPrograms = [];

        foreach (EventTypeEnum::values() as $eventType) {
            for ($j = 1; $j <= rand(1, 3); $j++) {
                $eventTypes[] = $eventType;
            }
        }

        for ($i = 0; $i < 2; $i++) {
            $testProgram = new Program(Uuid::uuid1()->toString(), $this->faker->text, ProgramTypeEnum::INDIVIDUAL);
            $testProgram->setStatus(ProgramStatusEnum::INPROGRESS);

            foreach ($eventTypes as $eventType) {
                $event = new Event($this->faker->name, $this->faker->text, $eventType);
                $event->setProgram($testProgram);
                $event->setStatus(EventStatusEnum::CREATED);

                for ($j = 1; $j < 3; $j++) {
                    $this->expectedRows++;
                    $user = new User(
                        self::$container->get(UserTypeRepository::class)->mustFindOneById(UserTypeEnum::CANDIDATE),
                        Uuid::uuid1()->toString(),
                        $this->faker->lastName,
                        $this->faker->email,
                        $this->faker->phoneNumber,
                    );
                    $user->setCoach(rand(1, 2) === 1 ? $this->loggedUser : null);
                    $user->setCompany(rand(1, 2) === 1 ? $company : null);
                    self::$container->get(UserRepository::class)->save($user);

                    $testProgram->addUserByProgramsUsers($user);
                    $event->addUser($user);
                }

                self::$container->get(EventRepository::class)->save($event);
            }

            self::$container->get(ProgramRepository::class)->save($testProgram);

            $this->testPrograms[] = $testProgram;
        }

        foreach (array_merge(EventStatusEnum::values(), ProgramStatusEnum::values()) as $value) {
            $this->statusLabels[$value] = $this->faker->name;
        }
    }

    public function testExportProgramUsers(): void
    {
        $envVarHelper = self::$container->get(EnvVarHelper::class);
        $file = self::$container->get(ExportEvents::class)->export($envVarHelper->fetch(EnvVarHelper::ROOT_PATH), $this->statusLabels);
        $reader = new Xlsx();
        $spreadsheet = array_filter($reader->load($file)->getActiveSheet()->toArray(), static fn(array $row) => ! empty($row[0]));

        $this->assertCount($this->expectedRows + 1, $spreadsheet);
        $this->assertCount(15, $spreadsheet[0]);

        foreach ($this->testPrograms as $program) {
            foreach ($program->getEvents() as $event) {
                $foundUsers = 0;
                foreach ($event->getUsers() as $user) {
                    foreach ($spreadsheet as $row) {
                        if ($row[0] !== $user->getLastName() || $row[1] !== $user->getFirstName()) {
                            continue;
                        }
                        $foundUsers++;
                        $this->assertEquals($user->getLastName(), $row[0]);
                        $this->assertEquals($user->getFirstName(), $row[1]);
                        $this->assertEquals($event->getName(), $row[12]);
                    }
                }
                $this->assertEquals(count($event->getUsers()), $foundUsers);
            }
        }
    }

    public function testExportEventsCrossTalent(): void
    {
        $content = self::$container->get(ExportEventsCrossTalent::class)->export();
        foreach ($this->testPrograms as $program) {
            foreach ($program->getEvents() as $event) {
                if ($event->getType() === EventTypeEnum::INDIVIDUAL_SESSION) {
                    $this->assertStringContainsString($event->getName(), $content);
                } else {
                    $this->assertStringNotContainsString($event->getName(), $content);
                }
            }
        }
    }
}
