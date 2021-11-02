<?php

declare(strict_types=1);

namespace Kreait\Firebase\Messaging;

use Kreait\Firebase\Exception\InvalidArgumentException;

final class MessageData implements \JsonSerializable
{
    /** @var array<string, string> */
    private array $data = [];

    private function __construct()
    {
    }

    /**
     * @param array<scalar|\Stringable, scalar|\Stringable> $data
     *
     * @throws InvalidArgumentException
     */
    public static function fromArray(array $data): self
    {
        $messageData = new self();

        foreach ($data as $key => $value) {
            $key = (string) $key;
            $value = (string) $value;

            if (self::isBinary($value)) {
                throw new InvalidArgumentException(
                    "The message data field '{$key}' seems to contain binary data. As this can lead to broken messages, "
                    .'please convert it to a string representation first, e.g. with bin2hex() or base64encode().'
                );
            }

            self::assertValidKey($key);

            $messageData->data[$key] = $value;
        }

        return $messageData;
    }

    /**
     * @return array<string, string>
     */
    public function jsonSerialize(): array
    {
        return $this->data;
    }

    private static function isBinary(string $value): bool
    {
        return \mb_detect_encoding($value, (array) \mb_detect_order(), true) === false;
    }

    /**
     * @see https://firebase.google.com/docs/cloud-messaging/concept-options#data_messages
     */
    private static function assertValidKey(string $value): void
    {
        $value = \mb_strtolower($value);
        // According to the docs, "notification" is reserved, but it's still accepted ¯\_(ツ)_/¯
        $reservedWords = ['from', /*'notification',*/ 'message_type'];
        $reservedPrefixes = ['google', 'gcm'];

        if (\in_array($value, $reservedWords, true)) {
            throw new InvalidArgumentException("'{$value}' is a reserved word and can not be used as a key in FCM data payloads");
        }

        foreach ($reservedPrefixes as $prefix) {
            if (\str_starts_with($value, $prefix)) {
                throw new InvalidArgumentException("'{$prefix}' is a reserved prefix and can not be used as a key in FCM data payloads");
            }
        }
    }
}
