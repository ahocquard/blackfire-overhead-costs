<?php

declare(strict_types=1);

namespace Acme\Value;

class ValueCollection
{
    /** @var ScalarValue[] */
    private $values;

    public function __construct(array $values)
    {
        $this->values = $values;
    }

    public function values(): array
    {
        return $this->values;
    }
}
