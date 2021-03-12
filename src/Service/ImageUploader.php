<?php


namespace App\Service;


use App\Util\UploadedBase64File;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class ImageUploader
 *
 * Simple file uploader.
 *
 * @package App\Service
 */
class ImageUploader
{
    public function __construct(private string $targetDirectory)
    { }

    public function upload(UploadedBase64File $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $fileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move($this->targetDirectory, $fileName);
        } catch (FileException $e) {
            throw $e;
        }

        return $fileName;
    }
}