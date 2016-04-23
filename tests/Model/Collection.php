<?php

namespace PSX\Project\Tests\Model;

/**
 * @Title("collection")
 * @Description("Collection result")
 */
class Collection
{
    /**
     * @Type("integer")
     */
    protected $totalResults;

    /**
     * @Type("array<PSX\Project\Tests\Model\Entity>")
     */
    protected $entry;

    public function getTotalResults()
    {
        return $this->totalResults;
    }

    public function setTotalResults($totalResults)
    {
        $this->totalResults = $totalResults;
    }

    public function getEntry()
    {
        return $this->entry;
    }

    public function setEntry($entry)
    {
        $this->entry = $entry;
    }
}
