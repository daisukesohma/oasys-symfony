<?php

declare(strict_types=1);

namespace App\Application\Program;

use App\Application\Event\CreateEventDraftFromEventModel;
use App\Domain\Enum\ProgramStatusEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Program;
use App\Domain\Model\User;
use App\Domain\Repository\CompanyRepository;
use App\Domain\Repository\ProgramCoachingIndividualRepository;
use App\Domain\Repository\ProgramModelRepository;
use App\Domain\Repository\ProgramRepository;
use App\Domain\Repository\UserRepository;
use function array_map;

final class CreateProgram
{
    private ProgramRepository $programRepository;
    private ProgramModelRepository $programModelRepository;
    private UserRepository $userRepository;
    private CreateEventDraftFromEventModel $createEventDraftFromEventModel;
    private ProgramCoachingIndividualRepository $programCoachingIndividualRepository;
    private CompanyRepository $companyRepository;

    public function __construct(ProgramRepository $programRepository, ProgramModelRepository $programModelRepository, UserRepository $userRepository, CreateEventDraftFromEventModel $createEventDraftFromEventModel, ProgramCoachingIndividualRepository $programCoachingIndividualRepository, CompanyRepository $companyRepository)
    {
        $this->programRepository = $programRepository;
        $this->programModelRepository = $programModelRepository;
        $this->userRepository = $userRepository;
        $this->programCoachingIndividualRepository = $programCoachingIndividualRepository;
        $this->createEventDraftFromEventModel = $createEventDraftFromEventModel;
        $this->companyRepository = $companyRepository;
    }

    /**
     * @param string[] $userIds
     * @param string[] $coachIds
     *
     * @throws NotFound
     */
    public function create(
        string $name,
        string $description,
        string $type,
        array $userIds,
        ?array $coachIds = [],
        ?string $modelId = null,
        ?int $period = null,
        ?string $companyId = null,
        bool $endSupportEmail = true,
        ?Program $program = null
    ): Program {
        $program = $program ? $program : new Program($name, $description, $type);
        $program->setStatus(ProgramStatusEnum::UPCOMING);
        $program->setPeriod($period);

        $programModel = null;
        if (! empty($modelId)) {
            $programModel = $this->programModelRepository->mustFindOneById($modelId);
            $program->setProgramModel($programModel);
        }
        $userBeans = [];
        foreach ($userIds as $user) {
            $userBeans[] = $this->userRepository->mustFindOneById($user);
        }
        $program->setUsersByProgramsUsers($userBeans);

        $currentUser = $this->userRepository->getLoggedUser();
        if (empty($coachIds)) {
            $program->setUsersByProgramsCoaches([$currentUser]);
        } else {
            /** @var User[] $coaches */
            $coaches = array_map(fn(string $coachId) => $this->userRepository->mustFindOneById($coachId), $coachIds);
            $program->setUsersByProgramsCoaches($coaches);
        }

        $program->setCompany($companyId === null ? null : $this->companyRepository->mustFindOneById($companyId));
        $program->setEndSupportEmail($endSupportEmail);
        $this->programRepository->save($program);

        if ($programModel !== null) {
            foreach ($programModel->getEventModels() as $eventModel) {
                $createdBy = $program->getCreatedBy();
                $this->createEventDraftFromEventModel->create($eventModel->getId(), $userIds, $createdBy !== null ? $createdBy->getId() : '', $program->getId());
            }
        }

        return $program;
    }
}
