<?php

declare(strict_types=1);

namespace App\Infrastructure\MongoDb;

use Prooph\EventMachine\Persistence\DocumentStore;
use Prooph\EventMachine\Persistence\DocumentStore\Filter\Filter;
use Prooph\EventMachine\Persistence\DocumentStore\Index;

final class MongoDocumentStore implements DocumentStore
{
    /**
     * @var MongoConnection
     */
    private $connection;

    public function __construct(MongoConnection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @return string[] list of all available collections
     */
    public function listCollections(): array
    {
        $cols = [];

        $cursor = $this->connection->client()->selectDatabase($this->connection->dbName())
            ->listCollections();

        foreach ($cursor as $info) {
            $cols[] = $info->getName();
        }

        return $cols;
    }

    /**
     * @param string $prefix
     * @return string[] of collection names
     */
    public function filterCollectionsByPrefix(string $prefix): array
    {
        $cols = $this->listCollections();

        return array_filter($cols, function (string $col) use ($prefix) {
            return strpos($col, $prefix) === 0;
        });
    }

    /**
     * @param string $collectionName
     * @return bool
     */
    public function hasCollection(string $collectionName): bool
    {
        $cols = $this->listCollections();

        return array_key_exists($collectionName, $cols);
    }

    /**
     * @param string $collectionName
     * @param Index[] ...$indices
     */
    public function addCollection(string $collectionName, Index ...$indices): void
    {
        //@TODO: Respect indices
        $col = $this->connection->client()->selectDatabase($this->connection->dbName())->selectCollection($collectionName);

        //Force creation of collection
        $col->insertOne(['_id' => 'tempdoc']);
        $col->deleteOne(['_id' => 'tempdoc']);
    }

    /**
     * @param string $collectionName
     * @throws \Throwable if dropping did not succeed
     */
    public function dropCollection(string $collectionName): void
    {
        $this->connection->selectCollection($collectionName)->drop();
    }

    /**
     * @param string $collectionName
     * @param string $docId
     * @param array $doc
     * @throws \Throwable if adding did not succeed
     */
    public function addDoc(string $collectionName, string $docId, array $doc): void
    {
        $doc['_id'] = $docId;

        $this->connection->selectCollection($collectionName)->insertOne($doc);
    }

    /**
     * @param string $collectionName
     * @param string $docId
     * @param array $docOrSubset
     * @throws \Throwable if updating did not succeed
     */
    public function updateDoc(string $collectionName, string $docId, array $docOrSubset): void
    {
        $this->connection->selectCollection($collectionName)->updateOne(['_id' => $docId], [
            '$set' => $docOrSubset
        ]);
    }

    /**
     * @param string $collectionName
     * @param Filter[] $filters
     * @param array $set
     * @throws \Throwable in case of connection error or other issues
     */
    public function updateMany(string $collectionName, array $filters, array $set): void
    {
        $this->connection->selectCollection($collectionName)->updateMany(
            $this->convertFilters($filters),
            ['$set' => $set]
        );
    }

    /**
     * Same as updateDoc except that doc is added to collection if it does not exist.
     *
     * @param string $collectionName
     * @param string $docId
     * @param array $docOrSubset
     * @throws \Throwable if insert/update did not succeed
     */
    public function upsertDoc(string $collectionName, string $docId, array $docOrSubset): void
    {
        $docOrSubset['_id'] = $docId;

        $this->connection->selectCollection($collectionName)->updateOne(['_id' => $docId], [
            '$set' => $docOrSubset,
        ], ['upsert' => true]);
    }

    /**
     * @param string $collectionName
     * @param string $docId
     * @throws \Throwable if deleting did not succeed
     */
    public function deleteDoc(string $collectionName, string $docId): void
    {
        $this->connection->selectCollection($collectionName)->deleteOne(['_id' => $docId]);
    }

    /**
     * @param string $collectionName
     * @param Filter[] $filters
     * @throws \Throwable in case of connection error or other issues
     */
    public function deleteMany(string $collectionName, array $filters): void
    {
        $this->connection->selectCollection($collectionName)->deleteMany($this->convertFilters($filters));
    }

    /**
     * @param string $collectionName
     * @param string $docId
     * @return array|null
     */
    public function getDoc(string $collectionName, string $docId): ?array
    {
        return $this->connection->selectCollection($collectionName)->findOne(['_id' => $docId]);
    }

    /**
     * @param string $collectionName
     * @param Filter[] $filters
     * @return \Traversable list of docs
     */
    public function filterDocs(string $collectionName, array $filters): \Traversable
    {
        return $this->connection->selectCollection($collectionName)->find($this->convertFilters($filters));
    }

    /**
     * @param Filter[] $filters
     * @return array
     */
    private function convertFilters(array $filters): array
    {
        $filterArr = [];

        foreach ($filters as $filter) {
            switch (get_class($filter)) {
                case DocumentStore\Filter\EqFilter::class:
                    /** @var DocumentStore\Filter\EqFilter $filter */
                    $filterArr[$filter->prop()] = ['$eq' => $filter->val()];
                    break;
                case DocumentStore\Filter\GtFilter::class:
                    /** @var DocumentStore\Filter\GtFilter $filter */
                    $filterArr[$filter->prop()] = ['$gt' => $filter->val()];
                    break;
                case DocumentStore\Filter\GteFilter::class:
                    /** @var DocumentStore\Filter\GteFilter $filter */
                    $filterArr[$filter->prop()] = ['$gte' => $filter->val()];
                    break;
                case DocumentStore\Filter\LtFilter::class:
                    /** @var DocumentStore\Filter\LtFilter $filter */
                    $filterArr[$filter->prop()] = ['$lt' => $filter->val()];
                    break;
                case DocumentStore\Filter\LteFilter::class:
                    /** @var DocumentStore\Filter\LteFilter $filter */
                    $filterArr[$filter->prop()] = ['$lte' => $filter->val()];
                    break;
                case DocumentStore\Filter\InArrayFilter::class:
                    /** @var DocumentStore\Filter\InArrayFilter $filter */
                    $filterArr[$filter->prop()] = ['$elemMatch' => [['$eq' => $filter->val()]]];
                    break;
                case DocumentStore\Filter\ExistsFilter::class:
                    /** @var DocumentStore\Filter\ExistsFilter $filter */
                    $filterArr[$filter->prop()] = ['$exists' => true];
                    break;
                case DocumentStore\Filter\AndFilter::class:
                    /** @var DocumentStore\Filter\AndFilter $filter */
                    $filterArr['$and'] = [$this->convertFilters([$filter->aFilter()]), $this->convertFilters([$filter->bFilter()])];
                    break;
                case DocumentStore\Filter\OrFilter::class:
                    /** @var DocumentStore\Filter\OrFilter $filter */
                    $filterArr['$or'] = [$this->convertFilters([$filter->aFilter()]), $this->convertFilters([$filter->bFilter()])];
                    break;
                default:
                    throw new \RuntimeException("Unsupported filter type. Got " . get_class($filter));
            }
        }

        return $filterArr;
    }
}
