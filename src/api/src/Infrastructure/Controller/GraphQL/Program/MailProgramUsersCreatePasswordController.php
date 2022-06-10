<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\Program;

use App\Application\Program\MailProgramUsersCreatePassword;
use App\Domain\Exception\NotFound;
use App\Domain\Model\Program;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class MailProgramUsersCreatePasswordController extends AbstractController
{
    private MailProgramUsersCreatePassword $mailProgramUsersCreatePassword;

    public function __construct(MailProgramUsersCreatePassword $mailProgramUsersCreatePassword)
    {
        $this->mailProgramUsersCreatePassword = $mailProgramUsersCreatePassword;
    }

    /**
     * @throws NotFound
     *
     * @Mutation
     * @Logged
     * @Right("ROLE_CREATE_PROGRAM")
     */
    public function mailProgramUsersCreatePassword(string $programId): Program
    {
        return $this->mailProgramUsersCreatePassword->mail($programId);
    }
}
