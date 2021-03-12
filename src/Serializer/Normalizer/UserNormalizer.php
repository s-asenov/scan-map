<?php

namespace App\Serializer\Normalizer;

use App\Entity\User;
use Symfony\Component\Serializer\Normalizer\CacheableSupportsMethodInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class UserNormalizer implements NormalizerInterface, CacheableSupportsMethodInterface
{
    /**
     * @param mixed|User $object
     * @param null $format
     * @param array $context
     * @return array
     */
    public function normalize(mixed $object, $format = null, array $context = []): array
    {
        $data = [
            'id' => $object->getId(),
            'email' => $object->getEmail(),
            'firstName' => $object->getFirstName(),
            'lastName' => $object->getLastName(),
            'createdOn' => $object->getCreatedAt(),
            'lastSeen' => $object->getLastSeen(),
            'apiToken' => $object->getApiToken(),
            'roles' => $object->getRoles(),
            'isVerified' => (int) $object->getIsVerified()
        ];

        return $data;
    }

    public function supportsNormalization($data, $format = null): bool
    {
        return $data instanceof User;
    }

    public function hasCacheableSupportsMethod(): bool
    {
        return true;
    }
}
