<?php

namespace App\Table;

use App\Model;
use App\Table\Generated\PopulationRow;
use App\Table\Generated\PopulationTable;
use PSX\DateTime\LocalDateTime;
use PSX\Http\Exception\NotFoundException;
use PSX\Sql\Condition;
use PSX\Sql\Sql;

class Population extends PopulationTable
{
    public function getCollection(?int $startIndex = null, ?int $count = null): Model\PopulationCollection
    {
        if (empty($startIndex) || $startIndex < 0) {
            $startIndex = 0;
        }

        if (empty($count) || $count < 1 || $count > 1024) {
            $count = 16;
        }

        $condition = new Condition();

        $result = $this->findAll($condition, $startIndex, $count, 'priority', Sql::SORT_DESC);
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
        $population->setInsertDate(LocalDateTime::from($row->getInsertDate()));
        return $population;
    }
}
