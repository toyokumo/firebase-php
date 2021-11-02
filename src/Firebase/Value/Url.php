<?php

declare(strict_types=1);

namespace Kreait\Firebase\Value;

use GuzzleHttp\Psr7\Uri;
use Kreait\Firebase\Exception\InvalidArgumentException;
use Psr\Http\Message\UriInterface;
use Throwable;

class Url implements \JsonSerializable, \Stringable
{
    /**
     * @internal
     */
    public function __construct(private UriInterface $value)
    {
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function fromValue(UriInterface|Url|string $value): self
    {
        if ($value instanceof UriInterface) {
            return new self($value);
        }

        if ($value instanceof self) {
            return new self($value->toUri());
        }

        try {
            return new self(new Uri($value));
        } catch (Throwable $e) {
            throw new InvalidArgumentException($e->getMessage());
        }
    }

    public function toUri(): UriInterface
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }

    public function jsonSerialize(): string
    {
        return (string) $this->value;
    }

    public function equalsTo(self|string $other): bool
    {
        return (string) $this->value === (string) $other;
    }
}
