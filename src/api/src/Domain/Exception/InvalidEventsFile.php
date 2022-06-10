<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use GraphQL\Error\ClientAware;
use RuntimeException;
use function Safe\json_encode;

class InvalidEventsFile extends RuntimeException implements ClientAware, DomainException
{
    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'invalid users excel file';
    }

    /**
     * @param mixed[] $errors
     */
    public function __construct(array $errors)
    {
        $message = json_encode($errors);
        parent::__construct($message, 400, null);
    }
}
