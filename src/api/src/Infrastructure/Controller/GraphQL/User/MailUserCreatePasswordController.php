<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Application\User\MailUserCreatePassword;
use App\Domain\Exception\NotFound;
use App\Domain\Model\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use TheCodingMachine\GraphQLite\Annotations\Logged;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use TheCodingMachine\GraphQLite\Annotations\Right;

final class MailUserCreatePasswordController extends AbstractController
{
    private MailUserCreatePassword $mailUserCreatePassword;

    public function __construct(MailUserCreatePassword $mailUserCreatePassword)
    {
        $this->mailUserCreatePassword = $mailUserCreatePassword;
    }

    /**
     * @throws NotFound
     *
     * @Mutation
     * @Logged
     * @Right("ROLE_CREATE_USER")
     */
    public function mailUserCreatePassword(string $userId): User
    {
        return $this->mailUserCreatePassword->mail($userId);
    }
}
