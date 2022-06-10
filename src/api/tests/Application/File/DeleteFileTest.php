<?php

declare(strict_types=1);

namespace App\Tests\Application\File;

use App\Application\File\DeleteFile;
use App\Domain\Exception\NotFound;
use App\Domain\Model\FileDescriptor;
use App\Domain\Repository\FileDescriptorRepository;
use App\Infrastructure\Config\EnvVarHelper;
use App\Tests\Application\ApplicationTestCase;
use Ramsey\Uuid\Uuid;
use function file_exists;
use function file_put_contents;
use function filesize;
use function mkdir;
use function rmdir;
use function scandir;
use function str_repeat;
use function unlink;

class DeleteFileTest extends ApplicationTestCase
{
    protected DeleteFile $deleteFile;
    protected string $basedir;
    protected FileDescriptor $file;
    protected EnvVarHelper $envVarHelper;

    protected function setUp(): void
    {
        parent::setUp();
        $this->deleteFile = self::$container->get(DeleteFile::class);
        $this->envVarHelper = self::$container->get(EnvVarHelper::class);

        $this->basedir = $this->envVarHelper->fetch(EnvVarHelper::ROOT_PATH) . 'uploads/test';
        if (! file_exists($this->basedir)) {
            mkdir($this->basedir, 0757, true);
        }
        file_put_contents($this->basedir . '/test.pdf', str_repeat('1212', 20480));

        $this->file = new FileDescriptor('test.pdf', filesize($this->basedir . '/test.pdf'), 'file:///uploads/test/test.pdf');
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

    public function testUploadFile(): void
    {
        $this->expectException(NotFound::class);
        $this->deleteFile->delete(Uuid::uuid1()->toString(), $this->envVarHelper->fetch(EnvVarHelper::ROOT_PATH));

        $this->deleteFile->delete($this->file->getId(), $this->envVarHelper->fetch(EnvVarHelper::ROOT_PATH));
        $this->assertFalse(file_exists($this->envVarHelper->fetch(EnvVarHelper::ROOT_PATH) . 'uploads/test/test.pdf'));
        $this->expectException(NotFound::class);
        self::$container->get(FileDescriptorRepository::class)->mustFindOneById($this->file->getId());
    }
}
