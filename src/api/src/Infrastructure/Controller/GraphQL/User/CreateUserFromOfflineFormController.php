<?php

declare(strict_types=1);

namespace App\Infrastructure\Controller\GraphQL\User;

use App\Application\User\CreateUserFromOfflineForm;
use App\Domain\Enum\CivilityEnum;
use App\Domain\Exception\InvalidStringValue;
use App\Domain\Exception\NotFound;
use ReCaptcha\ReCaptcha;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use TheCodingMachine\GraphQLite\Annotations\Mutation;
use function implode;

final class CreateUserFromOfflineFormController extends AbstractController
{
    private CreateUserFromOfflineForm $createUserFromOfflineForm;
    private ReCaptcha $recaptcha;

    public function __construct(CreateUserFromOfflineForm $createUserFromOfflineForm, ReCaptcha $recaptcha)
    {
        $this->createUserFromOfflineForm = $createUserFromOfflineForm;
        $this->recaptcha = $recaptcha;
    }

    /**
     * @throws NotFound
     * @throws InvalidStringValue
     *
     * @Mutation
     */
    public function createUserFromOfflineForm(
        Request $request,
        string $recaptchaToken,
        string $linkId,
        string $firstName,
        string $lastName,
        string $email,
        string $phone,
        string $civility = CivilityEnum::MISTER_CODE,
        ?string $address = null,
        ?string $linkedin = null,
        ?string $function = null,
        ?string $seniorityDate = null,
        ?string $previousFunction = null,
        ?string $service = null,
        ?string $birthDate = null,
        ?string $professionalCategory = null,
        ?string $annualCompensation = null,
        ?string $userCodePostal = null,
        ?string $userDepartment = null,
        ?string $userCity = null,
        ?string $workMode = null
    ): bool {
        $response = $this->recaptcha->verify($recaptchaToken, $request->getClientIp());

        if (! $response->isSuccess()) {
            throw new AccessDeniedHttpException('Recaptcha denied: ' . implode(', ', $response->getErrorCodes()));
        }

        $this->createUserFromOfflineForm->create(
            $linkId,
            $firstName,
            $lastName,
            $email,
            $phone,
            $civility,
            $address,
            $linkedin,
            $function,
            $seniorityDate,
            $previousFunction,
            $service,
            $birthDate,
            $professionalCategory,
            $annualCompensation,
            $userCodePostal,
            $userDepartment,
            $userCity,
            $workMode
        );

        return true;
    }
}
