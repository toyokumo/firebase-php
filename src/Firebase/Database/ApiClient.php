<?php

declare(strict_types=1);

namespace Kreait\Firebase\Database;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Psr7\Request;
use Kreait\Firebase\Exception\DatabaseApiExceptionConverter;
use Kreait\Firebase\Exception\DatabaseException;
use Kreait\Firebase\Util\JSON;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Throwable;

/**
 * @internal
 */
class ApiClient
{
    protected DatabaseApiExceptionConverter $errorHandler;

    /**
     * @internal
     */
    public function __construct(private ClientInterface $client)
    {
        $this->errorHandler = new DatabaseApiExceptionConverter();
    }

    /**
     * @throws DatabaseException
     */
    public function get(UriInterface|string $uri): mixed
    {
        $response = $this->requestApi('GET', $uri);

        return JSON::decode((string) $response->getBody(), true);
    }

    /**
     * @internal This method should only be used in the context of Database translations
     *
     * @throws DatabaseException
     *
     * @return array<string, mixed>
     */
    public function getWithETag(UriInterface|string $uri): array
    {
        $response = $this->requestApi('GET', $uri, [
            'headers' => [
                'X-Firebase-ETag' => 'true',
            ],
        ]);

        $value = JSON::decode((string) $response->getBody(), true);
        $etag = $response->getHeaderLine('ETag');

        return [
            'value' => $value,
            'etag' => $etag,
        ];
    }

    /**
     * @throws DatabaseException
     */
    public function set(UriInterface|string $uri, mixed $value): mixed
    {
        $response = $this->requestApi('PUT', $uri, ['json' => $value]);

        return JSON::decode((string) $response->getBody(), true);
    }

    /**
     * @internal This method should only be used in the context of Database transactions
     *
     * @throws DatabaseException
     */
    public function setWithEtag(UriInterface|string $uri, mixed $value, string $etag): mixed
    {
        $response = $this->requestApi('PUT', $uri, [
            'headers' => [
                'if-match' => $etag,
            ],
            'json' => $value,
        ]);

        return JSON::decode((string) $response->getBody(), true);
    }

    /**
     * @internal This method should only be used in the context of Database translations
     *
     * @throws DatabaseException
     */
    public function removeWithEtag(UriInterface|string $uri, string $etag): void
    {
        $this->requestApi('DELETE', $uri, [
            'headers' => [
                'if-match' => $etag,
            ],
        ]);
    }

    /**
     * @throws DatabaseException
     */
    public function updateRules(UriInterface|string $uri, RuleSet $ruleSet): mixed
    {
        $response = $this->requestApi('PUT', $uri, [
            'body' => JSON::encode($ruleSet, JSON_FORCE_OBJECT | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
        ]);

        return JSON::decode((string) $response->getBody(), true);
    }

    /**
     * @throws DatabaseException
     */
    public function push(UriInterface|string $uri, mixed $value): string
    {
        $response = $this->requestApi('POST', $uri, ['json' => $value]);

        return JSON::decode((string) $response->getBody(), true)['name'];
    }

    /**
     * @throws DatabaseException
     */
    public function remove(UriInterface|string $uri): void
    {
        $this->requestApi('DELETE', $uri);
    }

    /**
     * @param array<mixed> $values
     *
     * @throws DatabaseException
     */
    public function update(UriInterface|string $uri, array $values): void
    {
        $this->requestApi('PATCH', $uri, ['json' => $values]);
    }

    /**
     * @param array<string, mixed>|null $options
     *
     * @throws DatabaseException
     */
    private function requestApi(string $method, UriInterface|string $uri, ?array $options = null): ResponseInterface
    {
        $options ??= [];

        $request = new Request($method, $uri);

        try {
            return $this->client->send($request, $options);
        } catch (Throwable $e) {
            throw $this->errorHandler->convertException($e);
        }
    }
}
