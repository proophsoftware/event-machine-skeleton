<?php
declare(strict_types = 1);

namespace App\Infrastructure\MongoDb;

use MongoDB\Collection;
use Prooph\EventStore\Projection\AbstractReadModel;

abstract class MongoReadModel extends AbstractReadModel
{
    /**
     * Returns name of the target collection for this read model
     * @return string
     */
    abstract protected function projectionCollectionName(): string;

    /**
     * @var MongoConnection
     */
    private $connection;

    public function __construct(MongoConnection $mongoConnection)
    {
        $this->connection = $mongoConnection;
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
        $this->connection->client()->selectDatabase($this->connection->dbName())
            ->dropCollection($this->projectionCollectionName());
    }

    protected function insertOne(array $doc, $options = []): void
    {
        $this->getCollection()
            ->insertOne($doc, $options);
    }

    protected function updateOne(array $filter, array $update, array $options = []): void
    {
        $this->getCollection()
            ->updateOne($filter, $update, $options);
    }

    protected function updateMany(array $filter, array $update, array $options = []): void
    {
        $this->getCollection()
            ->updateMany($filter, $update, $options);
    }

    protected function getCollection(): Collection
    {
        return $this->connection->selectCollection($this->projectionCollectionName());
    }
}
