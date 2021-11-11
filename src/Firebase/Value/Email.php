<?php

declare(strict_types=1);

namespace Kreait\Firebase\Value;

use Kreait\Firebase\Exception\InvalidArgumentException;

/**
 * @internal
 */
final class Email implements \JsonSerializable, \Stringable
{
    private string $value;

    /**
     * @internal
     */
    public function __construct(string $value)
    {
        if (!\filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('The email address is invalid.');
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
