<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Value;

use GuzzleHttp\Psr7\Uri;
use InvalidArgumentException;
use Kreait\Firebase\Value\Url;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class UrlTest extends TestCase
{
    /**
     * @dataProvider validValues
     */
    public function testWithValidValue(Uri|string $value): void
    {
        $url = Url::fromValue($value);

        $check = (string) $value;

        $this->assertSame($check, (string) $url);
        $this->assertSame($check, $url->jsonSerialize());
        $this->assertTrue($url->equalsTo($check));
    }

    /**
     * @dataProvider invalidValues
     */
    public function testWithInvalidValue(string $value): void
    {
        $this->expectException(InvalidArgumentException::class);
        Url::fromValue($value);
    }

    /**
     * @return array<string, array<string|Uri>>
     */
    public function validValues(): array
    {
        return [
            'string' => ['https://domain.tld'],
            'Uri object' => [new Uri('https://domain.tld')],
        ];
    }

    /**
     * @return array<string, array<string>>
     */
    public function invalidValues(): array
    {
        return [
            'https:///domain.tld' => ['https:///domain.tld'],
            'http://:80' => ['http://:80'],
        ];
    }
}
