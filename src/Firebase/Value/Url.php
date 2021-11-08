<?php

declare(strict_types=1);

namespace Kreait\Firebase\Value;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

/**
 * @internal
 */
final class Url implements \JsonSerializable, \Stringable
{
    /**
     * @internal
     */
    private function __construct(private string $value)
    {
    }

    public static function fromValue(UriInterface|\Stringable|string $value): self
    {
        return new self((string) new Uri((string) $value));
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
