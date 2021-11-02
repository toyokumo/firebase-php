<?php

declare(strict_types=1);

namespace Kreait\Firebase\RemoteConfig;

class Condition implements \JsonSerializable
{
    private function __construct(private string $name, private string $expression, private ?TagColor $tagColor = null)
    {
    }

    /**
     * @phpstan-param array{
     *     name: string,
     *     expression: string,
     *     tagColor?: ?string
     * } $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['expression'],
            isset($data['tagColor']) ? new TagColor($data['tagColor']) : null
        );
    }

    public static function named(string $name): self
    {
        return new self($name, 'false', null);
    }

    public function name(): string
    {
        return $this->name;
    }

    public function expression(): string
    {
        return $this->expression;
    }

    public function withExpression(string $expression): self
    {
        $condition = clone $this;
        $condition->expression = $expression;

        return $condition;
    }

    public function withTagColor(TagColor|string $tagColor): self
    {
        $tagColor = $tagColor instanceof TagColor ? $tagColor : new TagColor($tagColor);

        $condition = clone $this;
        $condition->tagColor = $tagColor;

        return $condition;
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return \array_filter([
            'name' => $this->name,
            'expression' => $this->expression,
            'tagColor' => $this->tagColor?->value(),
        ], static fn ($value) => $value !== null);
    }
}
