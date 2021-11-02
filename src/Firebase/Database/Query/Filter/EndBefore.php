<?php

declare(strict_types=1);

namespace Kreait\Firebase\Database\Query\Filter;

use Kreait\Firebase\Database\Query\Filter;
use Kreait\Firebase\Database\Query\ModifierTrait;
use Kreait\Firebase\Util\JSON;
use Psr\Http\Message\UriInterface;

final class EndBefore implements Filter
{
    use ModifierTrait;

    public function __construct(private float|bool|int|string $value)
    {
    }

    public function modifyUri(UriInterface $uri): UriInterface
    {
        return $this->appendQueryParam($uri, 'endBefore', JSON::encode($this->value));
    }
}
