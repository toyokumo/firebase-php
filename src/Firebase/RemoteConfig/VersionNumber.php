<?php

declare(strict_types=1);

namespace Kreait\Firebase\RemoteConfig;

use Kreait\Firebase\Exception\InvalidArgumentException;

final class VersionNumber implements \JsonSerializable, \Stringable
{
    private function __construct(private string $value)
    {
    }

    public static function fromValue(int|string $value): self
    {
        $valueString = (string) $value;

        if (!\ctype_digit($valueString)) {
            throw new InvalidArgumentException('A version number should only consist of digits');
        }

        return new self($valueString);
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
