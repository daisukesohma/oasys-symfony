<?php
/*
 * This file has been automatically generated by TDBM.
 * You can edit this file as it will not be overwritten.
 */

declare(strict_types=1);

namespace App\Infrastructure\Dao;

use App\Domain\Enum\ResetPasswordTokenEnum;
use App\Domain\Exception\InvalidValue;
use App\Domain\Exception\NotFound;
use App\Domain\Model\ResetPasswordToken;
use App\Domain\Model\User;
use App\Domain\Repository\ResetPasswordTokenRepository;
use App\Infrastructure\Config\EnvVarHelper;
use App\Infrastructure\Dao\Generated\AbstractResetPasswordTokenDao;
use Firebase\JWT\JWT;
use TheCodingMachine\TDBM\TDBMService;
use UnexpectedValueException;
use function password_verify;
use function time;

/**
 * The ResetPasswordTokenDao class will maintain the persistence of ResetPasswordToken class into the reset_password_token table.
 */
class ResetPasswordTokenDao extends AbstractResetPasswordTokenDao implements ResetPasswordTokenRepository
{
    protected EnvVarHelper $envVarHelper;

    public function __construct(TDBMService $tdbmService, EnvVarHelper $envVarHelper)
    {
        parent::__construct($tdbmService);
        $this->envVarHelper = $envVarHelper;
    }

    public function encodeToken(User $user, string $accessToken): string
    {
        return JWT::encode([
            'sub' => 'reset',
            'exp' => time() + (3600 * 48),
            'aud' => $user->getId(),
            'accessToken' => $accessToken,
        ], $this->envVarHelper->fetch(EnvVarHelper::SECRET_ENV), ResetPasswordTokenEnum::ALGO);
    }

    public function mustCheckValidToken(string $token): ResetPasswordToken
    {
        try {
            $decodedToken = JWT::decode($token, $this->envVarHelper->fetch(EnvVarHelper::SECRET_ENV), [ResetPasswordTokenEnum::ALGO]);
            if (! isset($decodedToken->accessToken) || ! isset($decodedToken->aud)) {
                throw new NotFound(ResetPasswordToken::class, ['token' => $token]);
            }
            $resetPasswordToken = $this->findOne('user_id = :id', ['id' => $decodedToken->aud]);
            if (empty($resetPasswordToken)) {
                throw new NotFound(ResetPasswordToken::class, ['token' => $token]);
            }
            if (! password_verify($decodedToken->accessToken, $resetPasswordToken->getAccessToken())) {
                throw new InvalidValue('Given access token is invalid');
            }
        } catch (UnexpectedValueException $e) {
            throw new NotFound(ResetPasswordToken::class, ['token' => $token], $e);
        }

        return $resetPasswordToken;
    }

    public function findOneByUser(User $user): ?ResetPasswordToken
    {
        return $this->findOne('user_id = :user', ['user' => $user->getId()]);
    }
}