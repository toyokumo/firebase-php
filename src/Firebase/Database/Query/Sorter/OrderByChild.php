<?php

declare(strict_types=1);

namespace Kreait\Firebase\Database\Query\Sorter;

use function JmesPath\search;
use Kreait\Firebase\Database\Query\ModifierTrait;
use Kreait\Firebase\Database\Query\Sorter;
use Psr\Http\Message\UriInterface;

final class OrderByChild implements Sorter
{
    use ModifierTrait;

    public function __construct(private string $childKey)
    {
    }

    public function modifyUri(UriInterface $uri): UriInterface
    {
        return $this->appendQueryParam($uri, 'orderBy', \sprintf('"%s"', $this->childKey));
    }

    public function modifyValue(mixed $value): mixed
    {
        if (!\is_array($value)) {
            return $value;
        }

        $expression = \str_replace('/', '.', $this->childKey);

        \uasort($value, static fn ($a, $b) => search($expression, $a) <=> search($expression, $b));

        return $value;
    }
}
