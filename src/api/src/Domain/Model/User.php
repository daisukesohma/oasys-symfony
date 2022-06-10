<?php
/*
 * This file has been automatically generated by TDBM.
 * You can edit this file as it will not be overwritten.
 */

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Exception\InvalidStringValue;
use App\Domain\Logging\LoggableModel;
use App\Domain\Model\Generated\AbstractUser;
use Safe\DateTimeImmutable;
use TheCodingMachine\GraphQLite\Annotations\Field;
use TheCodingMachine\GraphQLite\Annotations\Type;
use function array_filter;
use function Safe\password_hash;
use const PASSWORD_DEFAULT;

/**
 * The User class maps the 'users' table in database.
 *
 * @Type
 */
class User extends AbstractUser implements LoggableModel
{
    /**
     * @throws InvalidStringValue
     */
    public function setFirstName(string $firstName): void
    {
        self::validateFirstName($firstName);
        parent::setFirstName($firstName);
    }

    /**
     * @throws InvalidStringValue
     */
    public function setLastName(string $lastName): void
    {
        self::validateLastName($lastName);
        parent::setLastName($lastName);
    }

    /**
     * @throws InvalidStringValue
     */
    public function setEmail(string $email): void
    {
        self::validateEmail($email);
        parent::setEmail($email);
    }

    /**
     * @throws InvalidStringValue
     */
    public function setPassword(?string $password): void
    {
        if ($password === null) {
            parent::setPassword(null);

            return;
        }

        self::validatePassword($password);
        parent::setPassword(password_hash($password, PASSWORD_DEFAULT));
    }

    /**
     * @throws InvalidStringValue
     */
    public function setPhone(string $phone): void
    {
        self::validatePhone($phone);
        parent::setPhone($phone);
    }

    /**
     * @throws InvalidStringValue
     */
    public function setCivility(string $civility): void
    {
        self::validateCivility($civility);
        parent::setCivility($civility);
    }

    /**
     * @return Role[]
     */
    public function getRoles(): array
    {
        return $this->getRolesByUsersRoles();
    }

    public function hasRight(string $rightToCheck): bool
    {
        foreach ($this->getRoles() as $role) {
            foreach ($role->getRights() as $right) {
                if ($right->getCode() === $rightToCheck) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * @throws InvalidStringValue
     */
    public static function validateFirstName(string $firstName): void
    {
        $property = 'firstName';
        InvalidStringValue::notBlank($firstName, $property);
        InvalidStringValue::length($firstName, 1, 255, $property);
    }

    /**
     * @throws InvalidStringValue
     */
    public static function validateLastName(string $lastName): void
    {
        $property = 'lastName';
        InvalidStringValue::notBlank($lastName, $property);
        InvalidStringValue::length($lastName, 1, 255, $property);
    }

    /**
     * @throws InvalidStringValue
     */
    public static function validateEmail(string $email): void
    {
        $property = 'email';
        InvalidStringValue::notBlank($email, $property);
        InvalidStringValue::length($email, 1, 255, $property);
        InvalidStringValue::email($email, $property);
    }

    /**
     * @throws InvalidStringValue
     */
    public static function validatePassword(string $password): void
    {
        $property = 'password';
        InvalidStringValue::password($password, $property);
    }

    /**
     * @throws InvalidStringValue
     */
    public static function validatePhone(string $phone): void
    {
        $property = 'phone';
        InvalidStringValue::phone($phone, $property);
    }

    /**
     * @throws InvalidStringValue
     */
    public static function validateCivility(string $civility): void
    {
        $property = 'civility';
        InvalidStringValue::notBlank($civility, $property);
        InvalidStringValue::civility($civility, $property);
    }

    /**
     * @throws InvalidStringValue
     */
    public function setUserInformation(User $user, string $firstName, string $lastName, string $phone, string $civility, ?string $address = null, ?string $linkedin = null, ?string $function = null, ?string $seniorityDate = null, ?string $previousFunction = null, ?FileDescriptor $profilePictureId = null): void
    {
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setPhone($phone);
        $user->setProfilePicture($profilePictureId);
        $user->setCivility($civility);
        $user->setAddress($address);
        $user->setLinkedin($linkedin);
        $user->setFunction($function);
        $user->setPreviousFunction($previousFunction);
        $user->setSeniorityDate($seniorityDate !== null ? new DateTimeImmutable($seniorityDate) : null);
    }

    /**
     * @return Program[]
     *
     * @Field
     */
    public function getProgramsByProgramsUsers(): array
    {
        return array_filter(parent::getProgramsByProgramsUsers(), static fn(Program $program) => ! $program->getDeleted());
    }
}