<?php

namespace App\Table;

use App\Model;
use App\Table\Generated\PopulationRow;
use App\Table\Generated\PopulationTable;
use PSX\Http\Exception\NotFoundException;
use PSX\Sql\Condition;
use PSX\Sql\OrderBy;

class Population extends PopulationTable
{
    public function getCollection(?int $startIndex = null, ?int $count = null): Model\PopulationCollection
    {
        if ($startIndex === null || $startIndex < 0) {
            $startIndex = 0;
        }

        if ($count === null || $count < 1 || $count > 1024) {
            $count = 16;
        }

        $condition = Condition::withAnd();

        $result = $this->findAll($condition, $startIndex, $count, self::COLUMN_PLACE, OrderBy::ASC);
        $entries = [];
        foreach ($result as $row) {
            $entries[] = $this->mapRowToModel($row);
        }

        $collection = new Model\PopulationCollection();
        $collection->setTotalResults($this->getCount($condition));
        $collection->setStartIndex($startIndex);
        $collection->setItemsPerPage($count);
        $collection->setEntry($entries);

        return $collection;
    }

    public function getEntity(int $id): Model\Population
    {
        $row = $this->find($id);
        if (!$row instanceof PopulationRow) {
            throw new NotFoundException('Provided id not found');
        }

        return $this->mapRowToModel($row);
    }

    private function mapRowToModel(PopulationRow $row): Model\Population
    {
        $population = new Model\Population();
        $population->setId($row->getId());
        $population->setPlace($row->getPlace());
        $population->setRegion($row->getRegion());
        $population->setPopulation($row->getPopulation());
        $population->setUsers($row->getUsers());
        $population->setWorldUsers($row->getWorldUsers());
        $population->setInsertDate($row->getInsertDate());
        return $population;
    }
}
