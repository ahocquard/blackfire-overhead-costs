<?php

declare(strict_types=1);

namespace Acme\Normalizer;

use Acme\Value\ScalarValue;
use Symfony\Component\Serializer\Exception\BadMethodCallException;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DummyNormalizer implements NormalizerInterface
{
    public function normalize($object, $format = null, array $context = [])
    {
        throw new BadMethodCallException();
    }

    public function supportsNormalization($data, $format = null)
    {
        return false;
    }
}
