<?php

declare(strict_types=1);

namespace Kreait\Firebase;

use GuzzleHttp\ClientInterface;
use InvalidArgumentException;
use Kreait\Firebase\DynamicLink\CreateDynamicLink;
use Kreait\Firebase\DynamicLink\DynamicLinkStatistics;
use Kreait\Firebase\DynamicLink\GetStatisticsForDynamicLink;
use Kreait\Firebase\DynamicLink\ShortenLongDynamicLink;
use Kreait\Firebase\Value\Url;
use Psr\Http\Message\UriInterface;

final class DynamicLinks implements Contract\DynamicLinks
{
    private ?string $defaultDynamicLinksDomain = null;

    private function __construct(private ClientInterface $apiClient)
    {
    }

    public static function withApiClient(ClientInterface $apiClient): self
    {
        return new self($apiClient);
    }

    public static function withApiClientAndDefaultDomain(ClientInterface $apiClient, string|\Stringable $dynamicLinksDomain): self
    {
        $service = self::withApiClient($apiClient);
        $service->defaultDynamicLinksDomain = (string) Url::fromValue($dynamicLinksDomain);

        return $service;
    }

    public function createUnguessableLink(mixed $url): DynamicLink
    {
        return $this->createDynamicLink($url, CreateDynamicLink::WITH_UNGUESSABLE_SUFFIX);
    }

    public function createShortLink(mixed $url): DynamicLink
    {
        return $this->createDynamicLink($url, CreateDynamicLink::WITH_SHORT_SUFFIX);
    }

    public function createDynamicLink(mixed $actionOrParametersOrUrl, ?string $suffixType = null): DynamicLink
    {
        $action = $this->ensureCreateAction($actionOrParametersOrUrl);

        if ($this->defaultDynamicLinksDomain && !$action->hasDynamicLinkDomain()) {
            $action = $action->withDynamicLinkDomain($this->defaultDynamicLinksDomain);
        }

        if ($suffixType === CreateDynamicLink::WITH_SHORT_SUFFIX) {
            $action = $action->withShortSuffix();
        } elseif ($suffixType === CreateDynamicLink::WITH_UNGUESSABLE_SUFFIX) {
            $action = $action->withUnguessableSuffix();
        }

        return (new CreateDynamicLink\GuzzleApiClientHandler($this->apiClient))->handle($action);
    }

    public function shortenLongDynamicLink(mixed $longDynamicLinkOrAction, ?string $suffixType = null): DynamicLink
    {
        $action = $this->ensureShortenAction($longDynamicLinkOrAction);

        if ($suffixType === ShortenLongDynamicLink::WITH_SHORT_SUFFIX) {
            $action = $action->withShortSuffix();
        } elseif ($suffixType === ShortenLongDynamicLink::WITH_UNGUESSABLE_SUFFIX) {
            $action = $action->withUnguessableSuffix();
        }

        return (new ShortenLongDynamicLink\GuzzleApiClientHandler($this->apiClient))->handle($action);
    }

    public function getStatistics(mixed $dynamicLinkOrAction, ?int $durationInDays = null): DynamicLinkStatistics
    {
        $action = $this->ensureGetStatisticsAction($dynamicLinkOrAction);

        if ($durationInDays) {
            $action = $action->withDurationInDays($durationInDays);
        }

        return (new DynamicLink\GetStatisticsForDynamicLink\GuzzleApiClientHandler($this->apiClient))->handle($action);
    }

    private function ensureCreateAction(mixed $actionOrParametersOrUrl): CreateDynamicLink
    {
        if ($this->isStringable($actionOrParametersOrUrl)) {
            return CreateDynamicLink::forUrl((string) $actionOrParametersOrUrl);
        }

        if (\is_array($actionOrParametersOrUrl)) {
            return CreateDynamicLink::fromArray($actionOrParametersOrUrl);
        }

        if ($actionOrParametersOrUrl instanceof CreateDynamicLink) {
            return $actionOrParametersOrUrl;
        }

        throw new InvalidArgumentException('Unsupported action');
    }

    private function ensureShortenAction(mixed $actionOrParametersOrUrl): ShortenLongDynamicLink
    {
        if ($this->isStringable($actionOrParametersOrUrl)) {
            return ShortenLongDynamicLink::forLongDynamicLink((string) $actionOrParametersOrUrl);
        }

        if (\is_array($actionOrParametersOrUrl)) {
            return ShortenLongDynamicLink::fromArray($actionOrParametersOrUrl);
        }

        if ($actionOrParametersOrUrl instanceof ShortenLongDynamicLink) {
            return $actionOrParametersOrUrl;
        }

        throw new InvalidArgumentException('Unsupported action');
    }

    private function ensureGetStatisticsAction(mixed $actionOrUrl): GetStatisticsForDynamicLink
    {
        if ($this->isStringable($actionOrUrl)) {
            return GetStatisticsForDynamicLink::forLink($actionOrUrl);
        }

        if ($actionOrUrl instanceof GetStatisticsForDynamicLink) {
            return $actionOrUrl;
        }

        throw new InvalidArgumentException('Unsupported action');
    }

    private function isStringable(mixed $value): bool
    {
        return \is_string($value) || $value instanceof UriInterface || (\is_object($value) && \method_exists($value, '__toString'));
    }
}
