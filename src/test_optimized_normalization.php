<?php

use Acme\Normalizer\DummyNormalizer;
use Acme\Normalizer\OptimizedNormalizationValueCollectionNormalizer;
use Acme\Normalizer\ScalarNormalizer;
use Acme\Normalizer\ValueCollectionNormalizer;
use Acme\Value\ScalarValue;
use Acme\Value\ValueCollection;
use Symfony\Component\Serializer\Serializer;

require '../vendor/autoload.php';

const NUMBER_PRODUCTS = 100;
const NUMBER_VALUES = 500;
const NUMBER_DUMMY_NORMALIZERS = 15;

$normalizers = array_map(function ($index) {return new DummyNormalizer();}, range(1, NUMBER_DUMMY_NORMALIZERS));
$normalizers[] = new OptimizedNormalizationValueCollectionNormalizer(new ScalarNormalizer());

$values = array_map(function ($index) {return new ScalarValue('foo', 'attribute_' . $index, 'en_US', 'ecommerce');}, range(1, NUMBER_VALUES));
$valueCollection = new ValueCollection($values);

$serializer = new Serializer($normalizers);

$start = microtime(true);
for ($i = 0; $i < NUMBER_PRODUCTS; $i++) {
    $normalizedValues = $serializer->normalize($valueCollection);
}

printf("normalization=%.4f\n", OptimizedNormalizationValueCollectionNormalizer::$normalizionTime);
printf("array_merge_recursive=%.4f\n", OptimizedNormalizationValueCollectionNormalizer::$mergeTime);
printf("total=%.4f\n", microtime(true) - $start);
