<?php

declare(strict_types=1);

namespace Kreait\Firebase\Database;

use Kreait\Firebase\Exception\Database\ReferenceHasNotBeenSnapshotted;
use Kreait\Firebase\Exception\Database\TransactionFailed;
use Kreait\Firebase\Exception\DatabaseException;

class Transaction
{
    /** @var string[] */
    private array $etags;

    /**
     * @internal
     */
    public function __construct(private ApiClient $apiClient)
    {
        $this->etags = [];
    }

    /**
     * @throws DatabaseException
     */
    public function snapshot(Reference $reference): Snapshot
    {
        $uri = (string) $reference->getUri();

        $result = $this->apiClient->getWithETag($uri);

        $this->etags[$uri] = $result['etag'];

        return new Snapshot($reference, $result['value']);
    }

    /**
     * @throws ReferenceHasNotBeenSnapshotted
     * @throws TransactionFailed
     */
    public function set(Reference $reference, mixed $value): void
    {
        $etag = $this->getEtagForReference($reference);

        try {
            $this->apiClient->setWithEtag($reference->getUri(), $value, $etag);
        } catch (DatabaseException $e) {
            throw TransactionFailed::onReference($reference, $e);
        }
    }

    /**
     * @throws ReferenceHasNotBeenSnapshotted
     * @throws TransactionFailed
     */
    public function remove(Reference $reference): void
    {
        $etag = $this->getEtagForReference($reference);

        try {
            $this->apiClient->removeWithEtag($reference->getUri(), $etag);
        } catch (DatabaseException $e) {
            throw TransactionFailed::onReference($reference, $e);
        }
    }

    /**
     * @throws ReferenceHasNotBeenSnapshotted
     */
    private function getEtagForReference(Reference $reference): string
    {
        $uri = (string) $reference->getUri();

        if (\array_key_exists($uri, $this->etags)) {
            return $this->etags[$uri];
        }

        throw new ReferenceHasNotBeenSnapshotted($reference);
    }
}
