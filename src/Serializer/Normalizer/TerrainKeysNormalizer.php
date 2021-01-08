<?php

namespace App\Serializer\Normalizer;

use App\Entity\TerrainKey;
use App\Util\MyHelper;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class TerrainKeysNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    private $helper;

    public function __construct(MyHelper $helper)
    {
        $this->helper = $helper;
    }

    public function normalize($object, $format = null, array $context = []): array
    {
        $data = [
            'id' => $object->getId(),
            'terrainId' => $object->getTerrain()->getId(),
            'createdOn' => $this->helper->formatDate($object->getCreatedOn()),
            'expiringOn' => $this->helper->formatDate($object->getExpiringOn())
        ];

        return $data;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof \App\Entity\TerrainKeys;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
