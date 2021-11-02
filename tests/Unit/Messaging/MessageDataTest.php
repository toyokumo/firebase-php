<?php

declare(strict_types=1);

namespace Kreait\Firebase\Tests\Unit\Messaging;

use InvalidArgumentException;
use Kreait\Firebase\Messaging\MessageData;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class MessageDataTest extends TestCase
{
    /**
     * @dataProvider validData
     *
     * @param array<string, mixed> $data
     */
    public function testItAcceptsValidData(array $data): void
    {
        MessageData::fromArray($data);
        $this->addToAssertionCount(1);
    }

    /**
     * @dataProvider invalidData
     *
     * @param array<string, mixed> $data
     */
    public function testItRejectsInvalidData(array $data): void
    {
        $this->expectException(InvalidArgumentException::class);
        MessageData::fromArray($data);
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function validData(): array
    {
        return [
            'integer' => [
                ['key' => 1],
            ],
            'float' => [
                ['key' => 1.23],
            ],
            'true' => [
                ['key' => true],
            ],
            'false' => [
                ['key' => false],
            ],
            'null' => [
                ['key' => null],
            ],
            'object with __toString()' => [
                ['key' => new class() implements \Stringable {
                    public function __toString(): string
                    {
                        return 'value';
                    }
                }],
            ],
            'UTF-8 string' => [
                ['key' => 'Jérôme'],
            ],
        ];
    }

    /**
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function invalidData(): iterable
    {
        return [
            // @see https://github.com/kreait/firebase-php/issues/441
            'binary data' => [
                ['key' => \hex2bin('81612bcffb')], // generated with \openssl_random_pseudo_bytes(5)
            ],
            'reserved_key_from' => [
                ['from' => 'any'],
            ],
            'reserved_key_message_type' => [
                ['message_type' => 'any'],
            ],
            'reserved_key_prefix_google' => [
                ['google_is_reserved' => 'any'],
            ],
            'reserved_key_prefix_gcm' => [
                ['gcm_is_reserved' => 'any'],
            ],
        ];
    }
}
