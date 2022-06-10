<?php

declare(strict_types=1);

namespace App\Application\Program;

use App\Application\User\CreateCandidateForProgramPicNotifier;
use App\Domain\Enum\ProgramTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Program;
use App\Domain\Model\ResetPasswordToken;
use App\Domain\Repository\ProgramRepository;
use App\Domain\Repository\ResetPasswordTokenRepository;
use App\Domain\Repository\UserRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use function Safe\ini_set;

final class MailProgramUsersCreatePassword
{
    private ProgramRepository $programRepository;
    private CreateCandidateForProgramPicNotifier $programPicNotifier;
    private ResetPasswordTokenRepository $resetPasswordTokenRepository;
    private UserRepository $userRepository;

    public function __construct(
        ProgramRepository $programRepository,
        CreateCandidateForProgramPicNotifier $programPicNotifier,
        ResetPasswordTokenRepository $resetPasswordTokenRepository,
        UserRepository $userRepository
    ) {
        $this->programRepository = $programRepository;
        $this->programPicNotifier = $programPicNotifier;
        $this->resetPasswordTokenRepository = $resetPasswordTokenRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @throws NotFound
     */
    public function mail(string $id): Program
    {
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', '0');
        $program = $this->programRepository->mustFindOneById($id);

        if ($program->getType() !== ProgramTypeEnum::PIC || $program->getUsersHaveBeenInvited()) {
            throw new AccessDeniedException('Can only notify for program');
        }

        foreach ($program->getUsersByProgramsUsers() as $user) {
            if ($user->getHasReceivedWelcomeMail()) {
                continue;
            }

            $accessToken = Uuid::uuid1()->toString();
            $resetPasswordToken = new ResetPasswordToken($user, $accessToken);
            $this->resetPasswordTokenRepository->save($resetPasswordToken);
            $tokenPassword = $this->resetPasswordTokenRepository->encodeToken($user, $accessToken);

            $this->programPicNotifier->notify($user, $tokenPassword);

            $user->setHasReceivedWelcomeMail(true);
            $this->userRepository->save($user);
        }

        $program->setUsersHaveBeenInvited(true);
        $this->programRepository->save($program);

        return $program;
    }
}
