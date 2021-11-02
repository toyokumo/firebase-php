<?php

declare(strict_types=1);

namespace Kreait\Firebase\Util;

use Kreait\Firebase\Exception\InvalidArgumentException;
use Throwable;

class JSON
{
    /**
     * Wrapper for JSON encoding that throws when an error occurs.
     *
     * Shamelessly copied from Guzzle.
     *
     * @internal
     *
     * @see \GuzzleHttp\json_encode()
     *
     * @phpstan-param int<0, max>|null $options JSON encode option bitmask
     * @phpstan-param int<1, max>|null $depth Set the maximum depth. Must be greater than zero
     *
     * @throws InvalidArgumentException if the JSON cannot be encoded
     */
    public static function encode(mixed $value, ?int $options = null, ?int $depth = null): string
    {
        $depth = $depth ?? 512;
        $options = $options ?? 0;

        try {
            return \json_encode($value, JSON_THROW_ON_ERROR | $options, $depth);
        } catch (Throwable $e) {
            throw new InvalidArgumentException('json_encode error: '.$e->getMessage());
        }
    }

    /**
     * Wrapper for json_decode that throws when an error occurs.
     *
     * Shamelessly copied from Guzzle.
     *
     * @internal
     *
     * @see \GuzzleHttp\json_encode()
     *
     * @param string $json JSON data to parse
     * @param bool|null $assoc When true, returned objects will be converted into associative arrays
     * @phpstan-param int<1, max>|null $depth User specified recursion depth
     * @phpstan-param int<0, max>|null $options Bitmask of JSON decode options
     *
     * @throws \InvalidArgumentException if the JSON cannot be decoded
     */
    public static function decode(string $json, ?bool $assoc = null, ?int $depth = null, ?int $options = null): mixed
    {
        $assoc = $assoc ?? null;
        $depth = $depth ?? 512;
        $options = $options ?? 0;

        try {
            return \json_decode($json, $assoc, $depth, JSON_THROW_ON_ERROR | $options);
        } catch (Throwable $e) {
            throw new InvalidArgumentException('json_decode error: '.$e->getMessage());
        }
    }

    /**
     * Returns true if the given value is a valid JSON string.
     *
     * @internal
     */
    public static function isValid(string $value): bool
    {
        try {
            self::decode($value);

            return true;
        } catch (Throwable) {
            return false;
        }
    }

    /**
     * @internal
     */
    public static function prettyPrint(mixed $value): string
    {
        return self::encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
    }
}
