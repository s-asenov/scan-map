<?php

namespace App\Serializer\Normalizer;

use App\Entity\TerrainKey;
use App\Util\MyHelper;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class TerrainKeysNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    public function __construct(private MyHelper $helper)
    { }

    /**
     * @param mixed $object
     * @param null $format
     * @param array $context
     * @return array
     */
    public function normalize(mixed $object, $format = null, array $context = []): array
    {
        return [
            'id' => $object->getId(),
            'terrainId' => $object->getTerrain()->getId(),
            'createdOn' => $this->helper->formatDate($object->getCreatedOn()),
            'expiringOn' => $this->helper->formatDate($object->getExpiringOn())
        ];
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof TerrainKey;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
