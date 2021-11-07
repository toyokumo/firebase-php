<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth;

use Firebase\Auth\Token\Domain\Generator;
use Kreait\Firebase\Exception\RuntimeException;
use Lcobucci\JWT\Token;

/**
 * @internal
 */
final class DisabledLegacyCustomTokenGenerator implements Generator
{
    public function __construct(private string $reason)
    {
    }

    public function createCustomToken($uid, array $claims = []): Token
    {
        throw new RuntimeException($this->reason);
    }
}
