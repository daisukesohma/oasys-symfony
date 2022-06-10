<?php

declare(strict_types=1);

namespace App\Domain\Model;

interface LoggedUser
{
    public function getEmail(): string;

    public function getId(): string;
}
