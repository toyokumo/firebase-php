<?php

declare(strict_types=1);

namespace Kreait\Firebase\RemoteConfig;

final class UpdateOrigin implements \JsonSerializable, \Stringable
{
    public const UNSPECIFIED = 'REMOTE_CONFIG_UPDATE_ORIGIN_UNSPECIFIED';
    public const CONSOLE = 'CONSOLE';
    public const REST_API = 'REST_API';

    private function __construct(private string $value)
    {
    }

    public static function fromValue(string $value): self
    {
        return new self($value);
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
