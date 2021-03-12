<?php

namespace App\Serializer\Normalizer;

use App\Entity\Terrain;
use DateTime;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class TerrainNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    public function __construct(private TerrainKeysNormalizer $keysNormalizer)
    {
    }

    public function normalize($object, $format = null, array $context = []): array
    {
        $keys = [];

        foreach ($object->getTerrainKeys() as $terrainKey) {
            if ($terrainKey->getExpiringOn() > new DateTime()) {
                $keys[] = $this->keysNormalizer->normalize($terrainKey);
            }
        }

        return [
            'id' => $object->getId(),
            'zipName' => $object->getZipName(),
            'user' => $object->getUser()->getId(),
            'name' => $object->getName(),
            'imageDirectory' => $object->getImageDirectory(),
            'terrainKeys' => $keys
        ];
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
