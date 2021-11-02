<?php

declare(strict_types=1);

namespace Kreait\Firebase\RemoteConfig;

class ConditionalValue implements \JsonSerializable
{
    /**
     * @internal
     */
    public function __construct(private string $conditionName, private string $value)
    {
    }

    public function conditionName(): string
    {
        return $this->conditionName;
    }

    public static function basedOn(string|Condition $condition): self
    {
        $name = $condition instanceof Condition ? $condition->name() : $condition;

        return new self($name, '');
    }

    public function value(): string
    {
        return $this->value;
    }

    public function withValue(string $value): self
    {
        $conditionalValue = clone $this;
        $conditionalValue->value = $value;

        return $conditionalValue;
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return ['value' => $this->value];
    }
}
