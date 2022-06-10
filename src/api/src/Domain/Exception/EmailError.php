<?php

declare(strict_types=1);

namespace App\Domain\Exception;

use GraphQL\Error\ClientAware;
use RuntimeException;

class EmailError extends RuntimeException implements ClientAware, DomainException
{
    public function isClientSafe(): bool
    {
        return true;
    }

    public function getCategory(): string
    {
        return 'invalid value';
    }

    public function __construct(string $context)
    {
        $message = 'Could not send email for ' . $context;
        parent::__construct($message, 403, null);
    }
}
