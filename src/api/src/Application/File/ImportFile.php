<?php

declare(strict_types=1);

namespace App\Application\File;

use App\Domain\Exception\InvalidFileValue;
use Psr\Http\Message\UploadedFileInterface;
use function file_exists;
use function Safe\filesize;
use function Safe\mkdir;

trait ImportFile
{
    /**
     * @throws InvalidFileValue
     */
    public function importFile(UploadedFileInterface $file, string $rootPath): string
    {
        InvalidFileValue::isNotEmpty($file, 'file');

        if (! file_exists($rootPath . 'uploads/imports')) {
            mkdir($rootPath . 'uploads/imports/', 0757, true);
        }
        $filepath = $rootPath . 'uploads/imports/tmp_import_' . $file->getClientFilename();
        $file->moveTo($filepath);
        if (! file_exists($filepath) || filesize($filepath) === 0) {
            throw new InvalidFileValue('Upload failed', 400);
        }

        return $filepath;
    }
}
