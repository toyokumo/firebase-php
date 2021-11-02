<?php

declare(strict_types=1);

namespace Kreait\Firebase\RemoteConfig;

final class ParameterGroup implements \JsonSerializable
{
    private string $description = '';

    /** @var array<string, Parameter> */
    private array $parameters = [];

    private function __construct(private string $name)
    {
    }

    public static function named(string $name): self
    {
        return new self($name);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function description(): string
    {
        return $this->description;
    }

    /**
     * @return array<string, Parameter>
     */
    public function parameters(): array
    {
        return $this->parameters;
    }

    public function withDescription(string $description): self
    {
        $group = clone $this;
        $group->description = $description;

        return $group;
    }

    public function withParameter(Parameter $parameter): self
    {
        $group = clone $this;
        $group->parameters[$parameter->name()] = $parameter;

        return $group;
    }

    /**
     * @phpstan-return array{
     *     description: string,
     *     parameters?: array<string, array{
     *         defaultValue: array<string, string|bool>,
     *         conditionalValues?: array<string, array<string, string>>,
     *         description: string
     *     }>
     * }
     */
    public function jsonSerialize(): array
    {
        $data = [
            'description' => $this->description,
        ];

        if ($this->parameters() !== []) {
            $parameters = [];

            foreach ($this->parameters as $name => $parameter) {
                $parameters[$name] = $parameter->jsonSerialize();
            }

            $data['parameters'] = $parameters;
        }

        return $data;
    }
}
