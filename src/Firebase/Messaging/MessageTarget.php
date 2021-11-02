<?php

declare(strict_types=1);

namespace Kreait\Firebase\Messaging;

use Kreait\Firebase\Exception\InvalidArgumentException;

final class MessageTarget
{
    public const CONDITION = 'condition';
    public const TOKEN = 'token';
    public const TOPIC = 'topic';

    /** @internal */
    public const UNKNOWN = 'unknown';

    public const TYPES = [
        self::CONDITION, self::TOKEN, self::TOPIC, self::UNKNOWN,
    ];

    private function __construct(private string $type, private string $value)
    {
    }

    /**
     * Create a new message target with the given type and value.
     *
     * @throws InvalidArgumentException
     */
    public static function with(string $type, string $value): self
    {
        $targetType = \mb_strtolower($type);

        $targetValue = match ($targetType) {
            self::CONDITION => (string) Condition::fromValue($value),
            self::TOKEN => (string) RegistrationToken::fromValue($value),
            self::TOPIC => (string) Topic::fromValue($value),
            self::UNKNOWN => $value,
            default => throw new InvalidArgumentException("Invalid target type '{$type}', valid types: ".\implode(', ', self::TYPES)),
        };

        return new self($targetType, $targetValue);
    }

    public function type(): string
    {
        return $this->type;
    }

    public function value(): string
    {
        return $this->value;
    }
}
