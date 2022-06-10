<?php

declare(strict_types=1);

namespace App\Tests\Application\User;

use App\Application\User\ExportUsersCrossTalent;
use App\Tests\Application\ApplicationTestCase;

final class ExportUsersTest extends ApplicationTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
    }

    public function testExportUsersCrossTalent(): void
    {
        $content = self::$container->get(ExportUsersCrossTalent::class)->export();
        $this->assertStringContainsString($this->loggedUser->getFirstName(), $content);
        $this->assertStringContainsString($this->loggedUser->getLastName(), $content);
    }
}
