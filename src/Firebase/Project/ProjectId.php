<?php

declare(strict_types=1);

namespace Kreait\Firebase\Project;

final class ProjectId
{
    private string $value = '';

    private function __construct()
    {
    }

    public static function fromString(string $value): self
    {
        $id = new self();
        $id->value = $value;

        return $id;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function sanitizedValue(): string
    {
        return \preg_replace('/[^A-Za-z0-9\-.:]/', '-', $this->value) ?: $this->value;
    }
}
