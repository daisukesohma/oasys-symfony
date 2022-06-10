<?php

declare(strict_types=1);

namespace App\Tests\Application\Company;

use App\Application\Document\DuplicateDocument;
use App\Domain\Enum\DocumentEnum;
use App\Domain\Enum\DocumentTypeEnum;
use App\Domain\Enum\UserTypeEnum;
use App\Domain\Model\Document;
use App\Domain\Model\FileDescriptor;
use App\Domain\Model\User;
use App\Domain\Repository\DocumentRepository;
use App\Domain\Repository\FileDescriptorRepository;
use App\Domain\Repository\UserRepository;
use App\Domain\Repository\UserTypeRepository;
use App\Infrastructure\Config\EnvVarHelper;
use App\Tests\Application\ApplicationTestCase;
use function file_exists;
use function file_put_contents;
use function filesize;
use function mkdir;
use function parse_url;
use function rmdir;
use function scandir;
use function str_repeat;
use function unlink;

class DuplicateDocumentTest extends ApplicationTestCase
{
    protected FileDescriptor $file;
    protected Document $document;
    protected User $author;
    protected string $basedir;
    protected EnvVarHelper $envVarHelper;

    protected function setUp(): void
    {
        parent::setUp();

        $userType = self::$container->get(UserTypeRepository::class)->mustFindOneById(UserTypeEnum::ADMINISTRATOR);
        $this->envVarHelper = self::$container->get(EnvVarHelper::class);

        $this->basedir = $this->envVarHelper->fetch(EnvVarHelper::ROOT_PATH) . 'public/files/test';
        if (! file_exists($this->basedir)) {
            mkdir($this->basedir, 0757, true);
        }
        file_put_contents($this->basedir . '/test.pdf', str_repeat('1212', 20480));

        $this->file = new FileDescriptor('test.pdf', filesize($this->basedir . '/test.pdf'), 'file:///public/files/test/test.pdf');

        $this->author = new User($userType, $this->faker->firstName, $this->faker->lastName, $this->faker->email, $this->faker->phoneNumber);
        $this->document = new Document($this->author, $this->faker->name, $this->faker->text, $this->faker->text, DocumentEnum::PUBLIC_CODE);
        $this->document->setType(DocumentTypeEnum::FILE);
        $this->document->setFileDescriptor($this->file);

        self::$container->get(UserRepository::class)->save($this->author);
        self::$container->get(DocumentRepository::class)->save($this->document);
        self::$container->get(FileDescriptorRepository::class)->save($this->file);
    }

    protected function tearDown(): void
    {
        foreach (scandir($this->basedir) as $file) {
            if ($file === '.' || $file === '..') {
                continue;
            }

            unlink($this->basedir . '/' . $file);
        }
        rmdir($this->basedir);

        parent::tearDown();
    }

    public function testDuplicateDocument(): void
    {
        $document = self::$container->get(DuplicateDocument::class)->duplicate($this->document->getId(), $this->envVarHelper->fetch(EnvVarHelper::ROOT_PATH));

        $this->assertEquals($this->document->getName(), $document->getName());
        $this->assertEquals($this->document->getVisibility(), $document->getVisibility());
        $this->assertEquals($this->document->getDescription(), $document->getDescription());
        $this->assertEquals($this->document->getType(), $document->getType());

        $upstream = parse_url($document->getFileDescriptor()->getUpstream());
        $this->assertTrue(file_exists($this->envVarHelper->fetch(EnvVarHelper::ROOT_PATH) . $upstream['path']));
    }
}
