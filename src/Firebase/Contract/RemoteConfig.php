<?php

declare(strict_types=1);

namespace Kreait\Firebase\Contract;

use Kreait\Firebase\Exception\RemoteConfig\ValidationFailed;
use Kreait\Firebase\Exception\RemoteConfig\VersionNotFound;
use Kreait\Firebase\Exception\RemoteConfigException;
use Kreait\Firebase\RemoteConfig\FindVersions;
use Kreait\Firebase\RemoteConfig\Template;
use Kreait\Firebase\RemoteConfig\Version;
use Kreait\Firebase\RemoteConfig\VersionNumber;
use Traversable;

/**
 * The Firebase Remote Config.
 *
 * @see https://firebase.google.com/docs/remote-config/use-config-rest
 * @see https://firebase.google.com/docs/remote-config/rest-reference
 */
interface RemoteConfig
{
    /**
     * @throws RemoteConfigException if something went wrong
     */
    public function get(): Template;

    /**
     * Validates the given template without publishing it.
     *
     * @param Template|array<string, mixed> $template
     *
     * @throws ValidationFailed if the validation failed
     * @throws RemoteConfigException
     */
    public function validate(Template|array $template): void;

    /**
     * @param Template|array<string, mixed> $template
     *
     * @throws RemoteConfigException
     *
     * @return string The etag value of the published template that can be compared to in later calls
     */
    public function publish(Template|array $template): string;

    /**
     * Returns a version with the given number.
     *
     * @throws VersionNotFound
     * @throws RemoteConfigException if something went wrong
     */
    public function getVersion(VersionNumber|int|string $versionNumber): Version;

    /**
     * Returns a version with the given number.
     *
     * @throws VersionNotFound
     * @throws RemoteConfigException if something went wrong
     */
    public function rollbackToVersion(VersionNumber|int|string $versionNumber): Template;

    /**
     * @param FindVersions|array<string, mixed>|null $query
     *
     * @throws RemoteConfigException if something went wrong
     *
     * @phpstan-return Traversable<Version>|Version[]
     */
    public function listVersions(array|FindVersions $query = null): Traversable;
}
