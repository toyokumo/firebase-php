<?php

declare(strict_types=1);

namespace Kreait\Firebase\Value;

class Provider implements \JsonSerializable, \Stringable
{
    public const ANONYMOUS = 'anonymous';
    public const CUSTOM = 'custom';
    public const FACEBOOK = 'facebook.com';
    public const FIREBASE = 'firebase';
    public const GITHUB = 'github.com';
    public const GOOGLE = 'google.com';
    public const PASSWORD = 'password';
    public const PHONE = 'phone';
    public const TWITTER = 'twitter.com';
    public const APPLE = 'apple.com';

    /**
     * @internal
     */
    public function __construct(private string $value)
    {
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
