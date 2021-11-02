<?php

declare(strict_types=1);

namespace Kreait\Firebase\Auth;

use Kreait\Firebase\Exception\InvalidArgumentException;
use Kreait\Firebase\Value\Uid;

final class DeleteUsersRequest
{
    private const MAX_BATCH_SIZE = 1000;
    /** @var string[] */
    private array $uids;

    /**
     * @param string[] $uids
     */
    private function __construct(private string $projectId, array $uids, private bool $enabledUsersShouldBeForceDeleted)
    {
        $this->uids = $uids;
    }

    /**
     * @param iterable<Uid|string> $uids
     */
    public static function withUids(string $projectId, iterable $uids, bool $forceDeleteEnabledUsers = false): self
    {
        $validatedUids = [];
        $count = 0;

        foreach ($uids as $uid) {
            $validatedUids[] = (string) (\is_string($uid) ? new Uid(\trim($uid)) : $uid);
            ++$count;

            if ($count > self::MAX_BATCH_SIZE) {
                throw new InvalidArgumentException('Only '.self::MAX_BATCH_SIZE.' users can be deleted at a time');
            }
        }

        return new self($projectId, $validatedUids, $forceDeleteEnabledUsers);
    }

    public function projectId(): string
    {
        return $this->projectId;
    }

    /**
     * @return string[]
     */
    public function uids(): array
    {
        return $this->uids;
    }

    public function enabledUsersShouldBeForceDeleted(): bool
    {
        return $this->enabledUsersShouldBeForceDeleted;
    }
}
