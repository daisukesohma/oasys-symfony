<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\ResetPasswordToken;
use App\Domain\Model\User;
use App\Domain\Repository\ResetPasswordTokenRepository;
use App\Domain\Repository\UserRepository;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

final class MailUserCreatePassword
{
    private UserRepository $userRepository;
    private ResetPasswordTokenRepository $resetPasswordTokenRepository;
    private CreateCandidateNotifier $createCandidateNotifier;
    private CreateUserNotifier $createUserNotifier;

    public function __construct(
        UserRepository $userRepository,
        ResetPasswordTokenRepository $resetPasswordTokenRepository,
        CreateCandidateNotifier $createCandidateNotifier,
        CreateUserNotifier $createUserNotifier
    ) {
        $this->userRepository = $userRepository;
        $this->resetPasswordTokenRepository = $resetPasswordTokenRepository;
        $this->createCandidateNotifier = $createCandidateNotifier;
        $this->createUserNotifier = $createUserNotifier;
    }

    /**
     * @throws NotFound
     */
    public function mail(string $id): User
    {
        $user = $this->userRepository->mustFindOneById($id);
        if ($user->getHasReceivedWelcomeMail()) {
            throw new AccessDeniedException('Cannot notify for the user');
        }

        $accessToken = Uuid::uuid1()->toString();
        $resetPasswordToken = new ResetPasswordToken($user, $accessToken);
        $this->resetPasswordTokenRepository->save($resetPasswordToken);
        $tokenPassword = $this->resetPasswordTokenRepository->encodeToken($user, $accessToken);
        $coach = $user->getCoach();

        if ($user->getType()->getId() === UserTypeEnum::CANDIDATE && ! empty($user->getProgramType()) && $coach !== null) {
            $this->createCandidateNotifier->notify($user, $tokenPassword, $user->getProgramType(), $coach);
        } else {
            $this->createUserNotifier->notify($user, $tokenPassword);
        }

        $user->setStatus(true);
        $user->setHasReceivedWelcomeMail(true);
        $this->userRepository->save($user);

        return $user;
    }
}
