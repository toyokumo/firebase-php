<?php

declare(strict_types=1);

namespace Kreait\Firebase\Exception\Auth;

use Kreait\Firebase\Exception\AuthException;
use Lcobucci\JWT\Token;
use RuntimeException;
use Throwable;

final class RevokedIdToken extends RuntimeException implements AuthException
{
    public function __construct(private Token $token, string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        $message = $message ?: 'The Firebase ID token has been revoked.';

        parent::__construct($message, $code, $previous);
    }

    public function getToken(): Token
    {
        return $this->token;
    }
}
