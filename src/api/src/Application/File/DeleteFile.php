<?php

declare(strict_types=1);

namespace App\Application\File;

use App\Domain\Exception\NotFound;
use App\Domain\Repository\FileDescriptorRepository;
use function file_exists;
use function Safe\parse_url;
use function Safe\unlink;

final class DeleteFile
{
    private FileDescriptorRepository $fileDescriptorRepository;

    public function __construct(FileDescriptorRepository $fileDescriptorRepository)
    {
        $this->fileDescriptorRepository = $fileDescriptorRepository;
    }

    /**
     * @throws NotFound
     */
    public function delete(string $fileDescriptorId, string $rootPath): void
    {
        $fileDescriptor = $this->fileDescriptorRepository->mustFindOneById($fileDescriptorId);

        $upstream = parse_url($fileDescriptor->getUpstream());
        if ($upstream['scheme'] === 'file' && empty($upstream['host'])) {
            $filepath = $rootPath . $upstream['path'];
            if (file_exists($filepath)) {
                unlink($rootPath . $upstream['path']);
            }
        }

        $this->fileDescriptorRepository->delete($fileDescriptor);
    }
}
