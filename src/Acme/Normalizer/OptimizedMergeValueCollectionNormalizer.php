<?php

declare(strict_types=1);

namespace Acme\Normalizer;

use Acme\Value\ValueCollection;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerAwareTrait;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class OptimizedMergeValueCollectionNormalizer implements NormalizerInterface, NormalizerAwareInterface
{
    use NormalizerAwareTrait;

    public static $normalizionTime = 0;

    public static $mergeTime = 0;

    public function normalize($valueCollection, $format = null, array $context = [])
    {
        $result = [];
        $normalizedValues = [];
        foreach ($valueCollection->values() as $value) {
            $start = microtime(true);
            $normalizedValues[] = $this->normalizer->normalize($value, $format, $context);
            self::$normalizionTime += microtime(true) - $start;
        }

        $start = microtime(true);
        $result = array_merge_recursive(...$normalizedValues);
        self::$mergeTime += microtime(true) - $start;

        return $result;
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof ValueCollection;
    }
}
