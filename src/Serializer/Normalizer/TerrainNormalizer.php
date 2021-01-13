<?php

namespace App\Serializer\Normalizer;

use App\Entity\Terrain;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class TerrainNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    private $normalizer;
    private $keysNormalizer;

    public function __construct(ObjectNormalizer $normalizer, TerrainKeysNormalizer $keysNormalizer)
    {
        $this->normalizer = $normalizer;
        $this->keysNormalizer = $keysNormalizer;
    }

    function handler($object, $format, $context)
    {
        return $object->getId();
    }

    public function normalize($object, $format = null, array $context = []): array
    {
        $keys = [];

        foreach ($object->getTerrainKeys() as $terrainKey) {
            if ($terrainKey->getExpiringOn() > new \DateTime()) {
                $keys[] = $this->keysNormalizer->normalize($terrainKey);
            }
        }

        $data = [
            'id' => $object->getId(),
            'zipName' => $object->getZipName(),
            'user' => $object->getUser()->getId(),
            'name' => $object->getName(),
            'imageDirectory' => $object->getImageDirectory(),
            'terrainKeys' => $keys
        ];

        return $data;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof Terrain;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
