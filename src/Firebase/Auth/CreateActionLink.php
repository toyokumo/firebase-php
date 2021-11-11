<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth;

use Kreait\Firebase\Auth\ActionCodeSettings\ValidatedActionCodeSettings;
use Kreait\Firebase\Value\Email;

final class CreateActionLink
{
    private ?string $tenantId = null;

    private function __construct(private string $type, private string $email, private ActionCodeSettings $settings)
    {
    }

    public static function new(string $type, \Stringable|string $email, ActionCodeSettings $settings, ?string $tenantId = null): self
    {
        $email = (string) (new Email((string) $email));

        $instance = new self($type, $email, $settings);
        $instance->tenantId = $tenantId;

        return $instance;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function settings(): ActionCodeSettings
    {
        return $this->settings ?? ValidatedActionCodeSettings::empty();
    }

    public function tenantId(): ?string
    {
        return $this->tenantId;
    }
}
