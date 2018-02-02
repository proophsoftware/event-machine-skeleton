<?php

declare(strict_types=1);

namespace App\Infrastructure\Building;

use App\Api\Payload;
use App\Api\Query;
use Prooph\EventMachine\Messaging\Message;
use Prooph\EventMachine\Persistence\DocumentStore;
use React\Promise\Deferred;

final class BuildingResolver
{
    /**
     * @var string
     */
    private $collectionName;

    /**
     * @var DocumentStore
     */
    private $documentStore;

    public function __construct(string $collectionName, DocumentStore $documentStore)
    {
        $this->collectionName = $collectionName;
        $this->documentStore = $documentStore;
    }

    public function __invoke(Message $query, Deferred $deferred): void
    {
        switch ($query->messageName()) {
            case Query::BUILDING:
                $this->resolveBuilding($query, $deferred);
                break;
            case Query::BUILDINGS:
                $this->resolveBuildings($query, $deferred);
                break;
            default:
                throw new \RuntimeException("Unknown query. Got " . $query->messageName());
        }
    }

    private function resolveBuildings(Message $getBuildings, Deferred $deferred): void
    {
        $filter = $getBuildings->getOrDefault(Payload::NAME, false) ?
            new DocumentStore\Filter\LikeFilter(Payload::NAME,'%' .$getBuildings->get(Payload::NAME). '%')
            : new DocumentStore\Filter\AnyFilter();

        $skip = $getBuildings->getOrDefault(Payload::SKIP, null);
        $limit = $getBuildings->getOrDefault(Payload::LIMIT, 10);

        $docs = $this->documentStore->filterDocs(
            $this->collectionName,
            $filter,
            $skip,
            $limit,
            DocumentStore\OrderBy\Asc::byProp(Payload::NAME)
        );

        $deferred->resolve(iterator_to_array($docs));
    }

    private function resolveBuilding(Message $getBuilding, Deferred $deferred): void
    {
        $building = $this->documentStore->getDoc(
            $this->collectionName,
            $getBuilding->get(Payload::BUILDING_ID)
        );

        if(!$building) {
            $deferred->reject(new \DomainException("Building not found", 404));
            return;
        }

        $deferred->resolve($building);
    }
}
