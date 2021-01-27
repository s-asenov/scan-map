<?php


namespace App\Service;


use App\Serializer\Normalizer\PlantNormalizer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;

class ZipSaver
{
    private $zipDir;
    private $imageBase64;
    private $zipName;

    public function __construct(string $zipDir, string $zipName, string $imageBase64)
    {
        $this->zipDir = $zipDir;
        $this->imageBase64 = $imageBase64;
        $this->zipName = $zipName;
    }

    private function encodePlants(array $plants): string
    {
        $plantNorm = new PlantNormalizer();
        $encoder = new JsonEncoder();
        $serializer = new Serializer([$plantNorm], [$encoder]);

        $json = $serializer->serialize($plants,  'json', ['json_encode_options' => \JSON_PRETTY_PRINT]);

        return $json;
    }

    /**
     * The method calls the zip service method to add the base64 encoded files
     * in a \ZipArchive.
     * @param array $plants
     */
    public function saveZip(array $plants): void
    {
        $jsonBase64 = base64_encode($this->encodePlants($plants));
        $zipService = new ZipService();

        $zipService->setServiceInfo($this->zipDir, $this->zipName);
        $zipService->addFiles([
            'json' => $jsonBase64,
            'jpg' => $this->imageBase64
        ]);
    }
}