<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth;

final class SignInWithEmailAndPassword implements IsTenantAware, SignIn
{
    private ?TenantId $tenantId = null;

    private function __construct(private string $email, private string $clearTextPassword)
    {
    }

    public static function fromValues(string $email, string $clearTextPassword): self
    {
        return new self($email, $clearTextPassword);
    }

    public function email(): string
    {
        return $this->email;
    }

    public function clearTextPassword(): string
    {
        return $this->clearTextPassword;
    }

    public function withTenantId(TenantId $tenantId): self
    {
        $action = clone $this;
        $action->tenantId = $tenantId;

        return $action;
    }

    public function tenantId(): ?TenantId
    {
        return $this->tenantId;
    }
}
