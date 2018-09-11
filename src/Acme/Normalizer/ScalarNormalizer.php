<?php

declare(strict_types=1);

namespace Acme\Normalizer;

use Acme\Value\ScalarValue;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class ScalarNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = [])
    {
        return [
            $object->attributeCode() => [
                $object->channelCode() => [
                    $object->localeCode() => $object->data()
                ]
            ]
        ];
    }

    public function supportsNormalization($data, $format = null)
    {
        return $data instanceof ScalarValue;
    }
}
