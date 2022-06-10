<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Enum\UserTypeEnum;
use App\Domain\Exception\NotFound;
use App\Domain\Model\User;
use App\Domain\Repository\CoachSpecialityRepository;
use App\Domain\Repository\UserRepository;

final class TransferCandidate
{
    private UserRepository $userRepository;
    private CoachSpecialityRepository $coachSpecialityRepository;

    public function __construct(UserRepository $userRepository, CoachSpecialityRepository $coachSpecialityRepository)
    {
        $this->userRepository = $userRepository;
        $this->coachSpecialityRepository = $coachSpecialityRepository;
    }

    /**
     * @throws NotFound
     */
    public function transfer(string $id, string $coachSpeciality): User
    {
        $user = $this->userRepository->mustFindOneById($id);
        if ($user->getType()->getId() !== UserTypeEnum::CANDIDATE) {
            throw new NotFound(User::class, ['id' => $id]);
        }

        $user->setHasBeenTransferred(true);
        $user->setCoachSpeciality($this->coachSpecialityRepository->mustFindOneById($coachSpeciality));
        $user->setCoach(null);

        $this->userRepository->save($user);

        return $user;
    }
}
