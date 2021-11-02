<?php

declare(strict_types=1);

namespace Kreait\Firebase\Value;

use Kreait\Firebase\Exception\InvalidArgumentException;

class Uid implements \JsonSerializable, \Stringable
{
    private string $value;

    /**
     * @internal
     */
    public function __construct(string $value)
    {
        if ($value === '' || \mb_strlen($value) > 128) {
            throw new InvalidArgumentException('A uid must be a non-empty string with at most 128 characters.');
        }

        $this->value = $value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function jsonSerialize(): string
    {
        return $this->value;
    }

    public function equalsTo(self|string $other): bool
    {
        return $this->value === (string) $other;
    }
}
