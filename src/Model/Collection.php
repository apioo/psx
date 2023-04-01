<?php

declare(strict_types = 1);

namespace App\Model;

/**
 * @template T
 */
class Collection implements \JsonSerializable
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
    public function getEntry() : ?array
    {
        return $this->entry;
    }
    public function jsonSerialize() : object
    {
        return (object) array_filter(array('totalResults' => $this->totalResults, 'startIndex' => $this->startIndex, 'itemsPerPage' => $this->itemsPerPage, 'entry' => $this->entry), static function ($value) : bool {
            return $value !== null;
        });
    }
}

