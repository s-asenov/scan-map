<?php


namespace App\Service;


use App\Entity\TerrainKey;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Class ZipService
 *
 * Responsible for the generation of zip files.
 *
 * @package App\Service
 */
class ZipService
{
    private $name;
    private $directory;

    public function setServiceInfo(string $directory, string $name)
    {
        $this->name = $name;
        $this->directory = $directory;
    }

    /**
     * @param array $files
     */
    public function addFiles(array $files): void
    {
        $zip = new \ZipArchive();

        $zip->open($this->directory.$this->name.".zip", \ZipArchive::CREATE);

        foreach ($files as $type => $content) {
            $this->addFile($zip, $type, $content);
        }

        $zip->close();
    }

    /**
     * Method responsible for saving the base64 string to file in the archive.
     *
     * @param \ZipArchive $zip
     * @param string $type
     * @param string $base64
     */
    private function addFile(\ZipArchive $zip, string $type, string $base64): void
    {
        $zip->addFromString($this->name . "." . $type, base64_decode($base64));
    }

    /**
     * @param string $zipDir
     * @param TerrainKey $terrainKey
     * @return false|mixed|SplFileInfo
     */
    public function getZip(string $zipDir, TerrainKey $terrainKey)
    {
        $finder = new Finder();

        $finder->files()->in($zipDir);

        foreach ($finder as $file) {
            $fileName = $file->getFilename();

            if ($fileName == $terrainKey->getTerrain()->getZipName().".zip") {
                return $file;
            }
        }

        return false;
    }
}