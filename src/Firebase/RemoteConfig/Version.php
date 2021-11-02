<?php

declare(strict_types=1);

namespace Kreait\Firebase\RemoteConfig;

use Kreait\Firebase\Util\DT;

final class Version
{
    private function __construct(private VersionNumber $versionNumber, private User $user, private string $description, private \DateTimeImmutable $updatedAt, private UpdateOrigin $updateOrigin, private UpdateType $updateType, private ?VersionNumber $rollbackSource)
    {
    }

    /**
     * @internal
     *
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $versionNumber = VersionNumber::fromValue($data['versionNumber']);
        $user = User::fromArray($data['updateUser']);
        $updatedAt = DT::toUTCDateTimeImmutable($data['updateTime']);
        $description = $data['description'] ?? '';

        $updateOrigin = ($data['updateOrigin'] ?? null)
            ? UpdateOrigin::fromValue($data['updateOrigin'])
            : UpdateOrigin::fromValue(UpdateOrigin::UNSPECIFIED);

        $updateType = ($data['updateType'] ?? null)
            ? UpdateType::fromValue($data['updateType'])
            : UpdateType::fromValue(UpdateType::UNSPECIFIED);

        $rollbackSource = ($data['rollbackSource'] ?? null)
            ? VersionNumber::fromValue($data['rollbackSource'])
            : null;

        return new self(
            $versionNumber,
            $user,
            $description,
            $updatedAt,
            $updateOrigin,
            $updateType,
            $rollbackSource
        );
    }

    public function versionNumber(): VersionNumber
    {
        return $this->versionNumber;
    }

    public function user(): User
    {
        return $this->user;
    }

    public function updatedAt(): \DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function description(): string
    {
        return $this->description;
    }

    public function updateOrigin(): UpdateOrigin
    {
        return $this->updateOrigin;
    }

    public function updateType(): UpdateType
    {
        return $this->updateType;
    }

    public function rollbackSource(): ?VersionNumber
    {
        return $this->rollbackSource;
    }
}
