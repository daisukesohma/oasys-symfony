<?php

declare(strict_types=1);

namespace App\Application\Program;

use App\Application\User\ImportUsersForProgramPic;
use App\Domain\Enum\ProgramTypeEnum;
use App\Domain\Exception\InvalidFileValue;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\InvalidUsersXlsx;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Program;
use App\Domain\Model\User;
use App\Domain\Repository\ProgramPicRepository;
use App\Domain\Repository\UserRepository;
use Psr\Http\Message\UploadedFileInterface;
use Ramsey\Uuid\Uuid;
use function array_map;

final class UpdateProgramPic
{
    private ProgramPicRepository $programRepository;
    private UpdateProgram $updateProgram;
    private UserRepository $userRepository;
    private ImportUsersForProgramPic $importUsersForProgramPic;

    public function __construct(ProgramPicRepository $programRepository, UpdateProgram $updateProgram, UserRepository $userRepository, ImportUsersForProgramPic $importUsersForProgramPic)
    {
        $this->programRepository = $programRepository;
        $this->updateProgram = $updateProgram;
        $this->userRepository = $userRepository;
        $this->importUsersForProgramPic = $importUsersForProgramPic;
    }

    /**
     * @param string[] $coachIds
     * @param string[] $userIds
     *
     * @throws NotFound
     * @throws InvalidStringValue
     * @throws InvalidUsersXlsx
     * @throws InvalidFileValue
     */
    public function update(
        string $id,
        string $name,
        string $description,
        array $coachIds,
        array $userIds,
        ?string $modelId = null,
        ?int $period = null,
        ?UploadedFileInterface $uploadedFile = null,
        ?string $rootPath = null,
        ?string $companyId = null,
        bool $endSupportEmail = true,
        int $appointmentTimeLimit = 0,
        ?string $inscriptionText = null
    ): Program {
        $program = $this->programRepository->mustFindOneById($id);

        $this->importUsersForProgramPic->setProgram($program);

        // If we are importing users then validate and save them first (for error checking)
        if ($uploadedFile !== null && $rootPath !== null) {
            $users = $this->importUsersForProgramPic->import($uploadedFile, $rootPath);
            foreach ($users as $user) {
                $program->addUserByProgramsUsers($user);
            }
            $program->setUsersHaveBeenInvited(false);
            $this->programRepository->save($program);

            $userIds = array_map(static fn(User $user) => $user->getId(), $program->getUsersByProgramsUsers());
        }

        $program->setAppointmentTimeLimit($appointmentTimeLimit);
        $program->setInscriptionText($inscriptionText);

        if ($program->getLinkId() === null) {
            $program->setLinkId(Uuid::uuid4()->toString());
        }

        return $this->updateProgram->update(
            $id,
            $name,
            $description,
            ProgramTypeEnum::PIC,
            $userIds,
            $coachIds,
            $modelId,
            null,
            null,
            null,
            null,
            null,
            null,
            $period,
            $companyId,
            $endSupportEmail
        );
    }
}
