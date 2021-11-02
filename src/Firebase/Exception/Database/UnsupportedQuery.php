<?php

declare(strict_types=1);

namespace Kreait\Firebase\Exception\Database;

use Kreait\Firebase\Database\Query;
use Kreait\Firebase\Exception\DatabaseException;
use RuntimeException;
use Throwable;

final class UnsupportedQuery extends RuntimeException implements DatabaseException
{
    public function __construct(private Query $query, string $message = '', int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getQuery(): Query
    {
        return $this->query;
    }
}
