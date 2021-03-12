<?php

namespace App\Util;

use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class UploadedBase64File
 *
 * The class extends the symfony component UploadedFile class
 * and makes it work with base64 string.
 *
 * @package App\Util
 */
class UploadedBase64File extends UploadedFile
{
    public function __construct(string $base64String, string $originalName)
    {
        $filePath = tempnam(sys_get_temp_dir(), 'UploadedFile');
        $data = base64_decode($base64String);
        file_put_contents($filePath, $data);
        $error = null;
        $mimeType = null;
        $test = true;

        parent::__construct($filePath, $originalName, $mimeType, $error, $test);
    }

}