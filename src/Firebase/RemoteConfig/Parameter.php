<?php

declare(strict_types=1);

namespace Kreait\Firebase\RemoteConfig;

use Kreait\Firebase\Exception\InvalidArgumentException;

class Parameter implements \JsonSerializable
{
    private string $description = '';

    /** @var ConditionalValue[] */
    private array $conditionalValues = [];

    private function __construct(
        private string $name,
        private DefaultValue $defaultValue
    ) {
    }

    public static function named(string $name, DefaultValue|string $defaultValue = null): self
    {
        if ($defaultValue === null) {
            $defaultValue = DefaultValue::none();
        } elseif (\is_string($defaultValue)) {
            $defaultValue = DefaultValue::with($defaultValue);
        } else {
            throw new InvalidArgumentException('The default value for a remote config parameter must be a string or NULL to use the in-app default.');
        }

        return new self($name, $defaultValue);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function withDescription(string $description): self
    {
        $parameter = clone $this;
        $parameter->description = $description;

        return $parameter;
    }

    public function withDefaultValue(DefaultValue|string $defaultValue): self
    {
        $defaultValue = $defaultValue instanceof DefaultValue ? $defaultValue : DefaultValue::with($defaultValue);

        $parameter = clone $this;
        $parameter->defaultValue = $defaultValue;

        return $parameter;
    }

    public function defaultValue(): DefaultValue
    {
        return $this->defaultValue;
    }

    public function withConditionalValue(ConditionalValue $conditionalValue): self
    {
        $parameter = clone $this;
        $parameter->conditionalValues[] = $conditionalValue;

        return $parameter;
    }

    /**
     * @return ConditionalValue[]
     */
    public function conditionalValues(): array
    {
        return $this->conditionalValues;
    }

    /**
     * @phpstan-return array{
     *     description: string,
     *     defaultValue: array<string, string|bool>,
     *     conditionalValues?: array<string, array<string, string>>,
     * }
     */
    public function jsonSerialize(): array
    {
        $data = [
            'description' => $this->description,
            'defaultValue' => $this->defaultValue->jsonSerialize(),
        ];

        if ($this->conditionalValues() !== []) {
            $conditionalValues = [];

            foreach ($this->conditionalValues() as $conditionalValue) {
                $conditionalValues[$conditionalValue->conditionName()] = $conditionalValue->jsonSerialize();
            }

            $data['conditionalValues'] = $conditionalValues;
        }

        return $data;
    }
}
