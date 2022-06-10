<?php

declare(strict_types=1);

namespace App\Application\User;

use App\Domain\Model\User;
use App\Domain\Repository\UserRepository;

final class ContactUs
{
    private UserRepository $userRepository;
    private ContactUsNotifier $contactUsNotifier;

    public function __construct(UserRepository $userRepository, ContactUsNotifier $contactUsNotifier)
    {
        $this->userRepository = $userRepository;
        $this->contactUsNotifier = $contactUsNotifier;
    }

    public function contact(string $comment): User
    {
        $this->contactUsNotifier->notify($this->userRepository->getLoggedUser(), $comment);

        return $this->userRepository->getLoggedUser();
    }
}
