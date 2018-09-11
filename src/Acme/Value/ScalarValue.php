<?php

declare(strict_types=1);

namespace Acme\Value;

class ScalarValue
{
    /** @var string */
    private $data;

    /** @var string */
    private $attributeCode;

    /** @var string */
    private $localeCode;

    /** @var string */
    private $channelCode;

    public function __construct(string $data, string $attributeCode, string $localeCode, string $channelCode)
    {
        $this->data = $data;
        $this->attributeCode = $attributeCode;
        $this->localeCode = $localeCode;
        $this->channelCode = $channelCode;
    }

    public function data(): string
    {
        return $this->data;
    }

    public function attributeCode(): string
    {
        return $this->attributeCode;
    }

    public function localeCode(): string
    {
        return $this->localeCode;
    }

    public function channelCode(): string
    {
        return $this->channelCode;
    }
}
