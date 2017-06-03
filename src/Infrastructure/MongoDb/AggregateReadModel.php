<?php
declare(strict_types = 1);

namespace App\Infrastructure\MongoDb;

use MongoDB\Collection;
use Prooph\Common\Messaging\Message;
use Prooph\EventMachine\Aggregate\Exception\AggregateNotFound;
use Prooph\EventMachine\EventMachine;
use Prooph\EventStore\Projection\AbstractReadModel;

final class AggregateReadModel extends AbstractReadModel
{
    const COLLECTION_PREFIX = 'aggregate_';
    /**
     * @var MongoConnection
     */
    private $connection;

    /**
     * @var EventMachine
     */
    private $eventMachine;

    public function __construct(MongoConnection $mongoConnection, EventMachine $eventMachine)
    {
        $this->connection = $mongoConnection;
        $this->eventMachine = $eventMachine;
    }


    public function init(): void
    {
        //nothing required here
    }

    public function isInitialized(): bool
    {
        return true;
    }

    public function reset(): void
    {
        $this->delete();
    }

    public function delete(): void
    {
        $collections = $this->connection->client()->selectDatabase($this->connection->dbName())->listCollections();

        foreach ($collections as $collectionInfo) {
            if(strpos($collectionInfo->getName(), self::COLLECTION_PREFIX) === 0) {
                $this->connection->client()->selectDatabase($this->connection->dbName())
                    ->dropCollection($collectionInfo->getName());
            }
        }
    }

    protected function upsert(Message $event, $options = []): void
    {
        $aggregateId = $event->metadata()['_aggregate_id'] ?? null;

        if(!$aggregateId) {
            return;
        }

        $aggregateType = $event->metadata()['_aggregate_type'] ?? null;

        if(!$aggregateType) {
            return;
        }

        try {
            $aggregateState = $this->eventMachine->loadAggregateState((string)$aggregateType, (string)$aggregateId);
        } catch (AggregateNotFound $e) {
            return;
        }


        $this->getCollection((string)$aggregateType)
            ->updateOne([
                '_id' => (string)$aggregateId,
            ], [
                '$set' => $aggregateState
            ], [
                'upsert'=>true
            ]);
    }

    protected function getCollection(string $aggregateType): Collection
    {
        return $this->connection->selectCollection($this->convertAggregateTypeToCollectionName($aggregateType));
    }

    private function convertAggregateTypeToCollectionName(string $aggregateType): string {
        $snake_case_aggregate_type = preg_replace('/(?<!^)[A-Z]/', '_$0', $aggregateType);
        return self::COLLECTION_PREFIX . mb_strtolower(preg_replace('/[^A-Za-z0-9]/', '_', $snake_case_aggregate_type));
    }
}
