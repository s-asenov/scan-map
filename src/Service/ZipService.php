<?php


namespace App\Service;


class ZipService
{
    private $name;
    private $directory;

    public function __construct(string $directory, string $name)
    {
        $this->name = $name;
        $this->directory = $directory;
    }

    public function addFiles(array $files)
    {
        $zip = new \ZipArchive();

        $zip->open($this->directory.$this->name.".zip", \ZipArchive::CREATE);

        foreach ($files as $type => $content) {
            $this->addFile($zip, $type, $content);
        }

        $zip->close();
    }

    private function addFile(\ZipArchive $zip, string $type, string $base64)
    {
        $zip->addFromString($this->name . "." . $type, base64_decode($base64));
    }
}