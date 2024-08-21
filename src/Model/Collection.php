<?php

declare(strict_types = 1);

namespace App\Model;

/**
 * @template T
 */
class Collection implements \JsonSerializable, \PSX\Record\RecordableInterface
{
    protected ?int $totalResults = null;
    protected ?int $startIndex = null;
    protected ?int $itemsPerPage = null;
    /**
     * @var array<T>|null
     */
    protected ?array $entry = null;
    public function setTotalResults(?int $totalResults) : void
    {
        $this->totalResults = $totalResults;
    }
    public function getTotalResults() : ?int
    {
        return $this->totalResults;
    }
    public function setStartIndex(?int $startIndex) : void
    {
        $this->startIndex = $startIndex;
    }
    public function getStartIndex() : ?int
    {
        return $this->startIndex;
    }
    public function setItemsPerPage(?int $itemsPerPage) : void
    {
        $this->itemsPerPage = $itemsPerPage;
    }
    public function getItemsPerPage() : ?int
    {
        return $this->itemsPerPage;
    }
    /**
     * @param array<T>|null $entry
     */
    public function setEntry(?array $entry) : void
    {
        $this->entry = $entry;
    }
    /**
     * @return array<T>|null
     */
    public function getEntry() : ?array
    {
        return $this->entry;
    }
    public function toRecord() : \PSX\Record\RecordInterface
    {
        /** @var \PSX\Record\Record<mixed> $record */
        $record = new \PSX\Record\Record();
        $record->put('totalResults', $this->totalResults);
        $record->put('startIndex', $this->startIndex);
        $record->put('itemsPerPage', $this->itemsPerPage);
        $record->put('entry', $this->entry);
        return $record;
    }
    public function jsonSerialize() : object
    {
        return (object) $this->toRecord()->getAll();
    }
}

